<?php

namespace LaravelEnso\Filters\App\Services;

use Carbon\Carbon;
use Closure;
use Illuminate\Support\Facades\Config;
use Iterator;
use LaravelEnso\Filters\App\DTOs\Segment;
use LaravelEnso\Filters\App\Enums\Intervals;
use LaravelEnso\Filters\App\Exceptions\Interval as Exception;

class Interval implements Iterator
{
    private string $type;
    private ?Carbon $min;
    private ?Carbon $max;
    private array $labels;
    private ?int $adjustment;
    private Carbon $start;
    private Carbon $end;
    private Closure $incrementer;
    private string $labelFormat;
    private int $key;

    public function __construct(string $type, ?Carbon $min = null, ?Carbon $max = null)
    {
        $this->type = $type;
        $this->min = $min;
        $this->max = $max;

        $this->validate();

        $this->labels = [];
        $this->adjustment = Intervals::adjustment($this->type);

        $this->scenario()->init();
    }

    public function labels(): array
    {
        return $this->labels;
    }

    public function current(): Segment
    {
        return new Segment($this->start, $this->end);
    }

    public function key(): int
    {
        return $this->key;
    }

    public function next(): void
    {
        $this->labels[] = $this->start->format($this->labelFormat);

        $incrementer = $this->incrementer;
        $incrementer($this->start);
        $incrementer($this->end);
        $this->key++;
    }

    public function rewind(): void
    {
        $this->scenario()->init();
    }

    public function valid(): bool
    {
        return $this->start->isBefore($this->max);
    }

    private function scenario(): self
    {
        switch ($this->type) {
            case Intervals::Today:
            case Intervals::Yesterday:
            case Intervals::Tomorrow:
                return $this->days()->hourly();
            case Intervals::ThisWeek:
            case Intervals::LastWeek:
            case Intervals::NextWeek:
                return $this->weeks()->daily();
            case Intervals::ThisMonth:
            case Intervals::LastMonth:
            case Intervals::NextMonth:
                return $this->months()->daily();
            case Intervals::ThisYear:
            case Intervals::LastYear:
            case Intervals::NextYear:
                return $this->years()->montly();
            case Intervals::Custom:
                return $this->custom();
        }
    }

    private function init(): void
    {
        $incrementer = $this->incrementer;
        $this->start = $this->min->copy();
        $this->end = $incrementer($this->start->copy());
        $this->key = 0;
    }

    private function hourly(): self
    {
        $this->incrementer = fn (Carbon $date) => $date->addHour();
        $this->labelFormat = 'H';

        return $this;
    }

    private function days(): self
    {
        $this->min = Carbon::today()->addDays($this->adjustment)->startOfDay();
        $this->max = Carbon::today()->addDays($this->adjustment)->endOfDay();

        return $this;
    }

    private function daily(): self
    {
        $this->incrementer = fn (Carbon $date) => $date->addDay();
        $this->labelFormat = Config::get('enso.config.dateFormat');

        return $this;
    }

    private function weeks(): self
    {
        $this->min = Carbon::today()->addWeeks($this->adjustment)->startOfWeek();
        $this->max = Carbon::today()->addWeeks($this->adjustment)->endOfWeek();

        return $this;
    }

    private function months(): self
    {
        $this->min = Carbon::today()->addMonths($this->adjustment)->startOfMonth();
        $this->max = Carbon::today()->addMonths($this->adjustment)->endOfMonth();

        return $this;
    }

    private function montly(): self
    {
        $this->incrementer = fn (Carbon $date) => $date->addMonth();
        $this->labelFormat = 'M-y';

        return $this;
    }

    private function years(): self
    {
        $this->min = Carbon::today()->addYears($this->adjustment)->startOfYear();
        $this->max = Carbon::today()->addYears($this->adjustment)->endOfYear();

        return $this;
    }

    private function yearly(): self
    {
        $this->incrementer = fn (Carbon $date) => $date->addYear();
        $this->labelFormat = 'Y';

        return $this;
    }

    private function custom(): self
    {
        $days = $this->max->diffInDays($this->min);

        if ($days === 1) {
            $this->hourly();
        } elseif ($days <= 31) {
            $this->daily();
        } elseif ($days <= 365) {
            $this->montly();
        } else {
            $this->yearly();
        }

        return $this;
    }

    private function validate(): void
    {
        if (! Intervals::keys()->contains($this->type)) {
            throw Exception::type($this->type);
        }

        if ($this->type === Intervals::Custom) {
            if (! $this->min || ! $this->max) {
                throw Exception::limit();
            }

            if ($this->min->isAfter($this->max)) {
                throw Exception::interval();
            }
        }
    }
}
