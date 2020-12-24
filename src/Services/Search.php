<?php

namespace LaravelEnso\Filters\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use LaravelEnso\Filters\Enums\ComparisonOperators;
use LaravelEnso\Filters\Enums\SearchModes;
use LaravelEnso\Filters\Exceptions\ComparisonOperator;
use LaravelEnso\Filters\Exceptions\SearchMode;

class Search
{
    private Builder $query;
    private Collection $attributes;
    private $search;
    private ?Collection $relations;
    private string $searchMode;
    private ComparisonOperators $operators;
    private string $comparisonOperator;
    private static array $algolia;

    public function __construct(Builder $query, array $attributes, $search)
    {
        $this->query = $query;
        $this->attributes = new Collection($attributes);
        $this->search = $search;
        $this->searchMode = SearchModes::Full;
        $this->operators = App::make(ComparisonOperators::class);
        $this->comparisonOperator = $this->operators::Like;
        $this->relations = null;
    }

    public function relations(array $relations): self
    {
        $this->relations = new Collection($relations);

        return $this;
    }

    public function searchMode(string $searchMode): self
    {
        if (! SearchModes::keys()->contains($searchMode)) {
            throw SearchMode::unknown();
        }

        $this->searchMode = $searchMode;

        $this->syncOperator();

        return $this;
    }

    public function comparisonOperator(string $comparisonOperator): self
    {
        if (! $this->operators::keys()->contains($comparisonOperator)) {
            throw ComparisonOperator::unknown();
        }

        $this->comparisonOperator = $comparisonOperator;

        $this->syncOperator();

        return $this;
    }

    public function handle(): Builder
    {
        if ($this->searchMode === SearchModes::Algolia) {
            return $this->algolia();
        }

        $excepted = new Collection([null, '']);

        $this->searchArguments()->reject(fn ($argument) => $excepted->containsStrict($argument))
            ->each(fn ($argument) => $this->query
                ->where(fn ($query) => $this->matchArgument($query, $argument)));

        return $this->query;
    }

    private function algolia(): Builder
    {
        $model = $this->query->getModel();
        $table = $this->query->getModel()->getTable();
        $key = $this->query->getModel()->getKeyName();
        $keys = $this->algoliaKeys($model, $key);

        return $this->query->whereIn("{$table}.{$key}", $keys);
    }

    private function algoliaKeys(Model $model, string $key): array
    {
        $table = $model->getTable();

        if (! isset(self::$algolia[$table][$this->search])) {
            $paginator = $model::search($this->search)->paginate(100)->toArray();

            $keys = (new Collection($paginator['data']))->pluck($key)->toArray();

            self::$algolia[$table][$this->search] = $keys;
        }

        return self::$algolia[$table][$this->search];
    }

    private function syncOperator()
    {
        if ($this->searchMode === SearchModes::ExactMatch) {
            $this->comparisonOperator = $this->operators::Equal;
        } elseif ($this->searchMode === SearchModes::DoesntContain) {
            $this->comparisonOperator = $this->operators::invert($this->comparisonOperator);
        }
    }

    private function searchArguments(): Collection
    {
        return $this->searchMode === SearchModes::Full
            ? new Collection(explode(' ', $this->search))
            : new Collection($this->search);
    }

    private function matchArgument(Builder $query, $argument): void
    {
        $this->attributes->each(fn ($attribute) => $query
            ->orWhere(fn ($query) => $this->matchAttribute($query, $attribute, $argument)));

        if (! $this->relations) {
            return;
        }

        $this->relations->each(fn ($attribute) => $query
            ->orWhere(fn ($query) => $this->matchAttribute($query, $attribute, $argument, true)));
    }

    private function matchAttribute(Builder $query, string $attribute, $argument, bool $relation = false): void
    {
        $query->when(
            $relation && $this->isNested($attribute),
            fn ($query) => $this->matchSegments($query, $attribute, $argument),
            fn ($query) => $query->where($attribute, $this->comparisonOperator, $this->wildcards($argument))
        );
    }

    private function isNested($attribute): bool
    {
        return Str::contains($attribute, '.');
    }

    private function matchSegments(Builder $query, string $attribute, $argument)
    {
        $attributes = (new Collection(explode('.', $attribute)));

        $query->whereHas($attributes->shift(), fn ($query) => $this
            ->matchAttribute($query, $attributes->implode('.'), $argument, true));
    }

    private function wildcards($argument): string
    {
        switch ($this->searchMode) {
            case SearchModes::Full:
            case SearchModes::DoesntContain:
                return '%'.$argument.'%';
            case SearchModes::StartsWith:
                return $argument.'%';
            case SearchModes::EndsWith:
                return '%'.$argument;
            case SearchModes::ExactMatch:
                return is_bool($argument) ? (int) $argument : $argument;
        }
    }
}
