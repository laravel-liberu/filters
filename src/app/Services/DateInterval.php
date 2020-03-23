<?php

namespace LaravelEnso\Filters\App\Services;

use Carbon\Carbon;
use Closure;
use Illuminate\Support\Facades\Config;
use LaravelEnso\Filters\App\Enums\DateIntervals as Intervals;
use LaravelEnso\Filters\App\Exceptions\InvalidArgument;

class DateInterval
{
    private string $type;
    private ?Carbon $min;
    private ?Carbon $max;
    private array $labels;
    private ?int $adjustment;
    private Carbon $start;
    private Carbon $end;
    private Closure $incrementer;
    private Closure $formatter;
    private string $labelFormat;

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

    public function start(): Carbon
    {
        $formatter = $this->formatter;

        return $formatter($this->start);
    }

    public function end(): Carbon
    {
        $formatter = $this->formatter;

        return $formatter($this->end);
    }

    public function labels(): array
    {
        return $this->labels;
    }

    public function next(): void
    {
        $this->labels[] = $this->start->format($this->labelFormat);

        $incrementer = $this->incrementer;
        $incrementer($this->start);
        $incrementer($this->end);
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
                $this->days()->hourly();
                break;
            case Intervals::ThisWeek:
            case Intervals::LastWeek:
            case Intervals::NextWeek:
                $this->weeks()->daily();
                break;
            case Intervals::ThisMonth:
            case Intervals::LastMonth:
            case Intervals::NextMonth:
                $this->months()->daily();
                break;
            case Intervals::ThisYear:
            case Intervals::LastYear:
            case Intervals::NextYear:
                $this->years()->montly();
                break;
            case Intervals::Custom:
            case Intervals::All:
                $this->custom();
        }

        return $this;
    }

    private function init(): void
    {
        $formatter = $this->formatter;
        $incrementer = $this->incrementer;
        $this->start = $formatter($this->min->copy());
        $this->end = $incrementer($this->start->copy());
    }

    private function hourly(): void
    {
        $this->incrementer = fn (Carbon $date) => $date->addHour();
        $this->formatter = fn (Carbon $date) => $date->startOfHour();
        $this->labelFormat = 'H';
    }

    private function days(): self
    {
        if (! Intervals::isManual($this->type)) {
            $this->min = Carbon::today()->addDays($this->adjustment)->startOfDay();
            $this->max = Carbon::today()->addDays($this->adjustment)->endOfDay();
        }

        return $this;
    }

    private function daily(): void
    {
        $this->incrementer = fn (Carbon $date) => $date->addDay();
        $this->formatter = fn (Carbon $date) => $date->startOfDay();
        $this->labelFormat = Config::get('enso.config.dateFormat');
    }

    private function weeks(): self
    {
        if (! Intervals::isManual($this->type)) {
            $this->min = Carbon::today()->addWeeks($this->adjustment)->startOfWeek();
            $this->max = Carbon::today()->addWeeks($this->adjustment)->endOfWeek();
        }

        return $this;
    }

    private function months(): self
    {
        if (! Intervals::isManual($this->type)) {
            $this->min = Carbon::today()->addMonths($this->adjustment)->startOfMonth();
            $this->max = Carbon::today()->addMonths($this->adjustment)->endOfMonth();
        }

        return $this;
    }

    private function montly(): void
    {
        $this->incrementer = fn (Carbon $date) => $date->addMonth();
        $this->formatter = fn (Carbon $date) => $date->startOfMonth();
        $this->labelFormat = 'M-y';
    }

    private function years(): self
    {
        if (! Intervals::isManual($this->type)) {
            $this->min = Carbon::today()->addYears($this->adjustment)->startOfYear();
            $this->max = Carbon::today()->addYears($this->adjustment)->endOfYear();
        }

        return $this;
    }

    private function yearly(): void
    {
        $this->incrementer = fn (Carbon $date) => $date->addYear();
        $this->formatter = fn (Carbon $date) => $date->startOfYear();
        $this->labelFormat = 'Y';
    }

    private function custom(): void
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
    }

    private function validate(): void
    {
        if (! Intervals::keys()->contains($this->type)) {
            throw InvalidArgument::type($this->type);
        }

        if (Intervals::isManual($this->type)) {
            if (! $this->min || ! $this->max) {
                throw InvalidArgument::limit();
            }

            if ($this->min->isAfter($this->max)) {
                throw InvalidArgument::interval();
            }
        }
    }
}
