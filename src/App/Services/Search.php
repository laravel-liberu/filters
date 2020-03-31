<?php

namespace LaravelEnso\Filters\App\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use LaravelEnso\Filters\App\Enums\ComparisonOperators;
use LaravelEnso\Filters\App\Enums\SearchModes;
use LaravelEnso\Filters\App\Exceptions\ComparisonOperator;
use LaravelEnso\Filters\App\Exceptions\SearchMode;

class Search
{
    private Builder $query;
    private Collection $attributes;
    private $search;
    private ?Collection $relations;
    private string $searchMode;
    private string $comparisonOperator;

    public function __construct(Builder $query, array $attributes, $search)
    {
        $this->query = $query;
        $this->attributes = new Collection($attributes);
        $this->search = $search;
        $this->searchMode = SearchModes::Full;
        $this->comparisonOperator = ComparisonOperators::Like;
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
        if (! ComparisonOperators::keys()->contains($comparisonOperator)) {
            throw ComparisonOperator::unknown();
        }

        $this->comparisonOperator = $comparisonOperator;

        $this->syncOperator();

        return $this;
    }

    public function handle(): Builder
    {
        $excepted = new Collection([null, '']);

        $this->searchArguments()->reject(fn ($argument) => $excepted->containsStrict($argument))
            ->each(fn ($argument) => $this->query
                ->where(fn ($query) => $this->matchArgument($query, $argument)));

        return $this->query;
    }

    private function syncOperator()
    {
        if ($this->searchMode === SearchModes::ExactMatch) {
            $this->comparisonOperator = ComparisonOperators::Equal;
        } elseif ($this->searchMode === SearchModes::DoesntContain) {
            if ($this->comparisonOperator = ComparisonOperators::invert($this->comparisonOperator));
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
