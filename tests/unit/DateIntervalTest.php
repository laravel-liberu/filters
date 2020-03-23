<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use LaravelEnso\Filters\App\Enums\DateIntervals;
use LaravelEnso\Filters\App\Services\DateInterval;
use Tests\TestCase;

class DateIntervalTest extends TestCase
{
    private string $type;
    private ?Carbon $min;
    private ?Carbon $max;
    private Carbon $initialStart;
    private Carbon $initialEnd;
    private Closure $incrementer;
    private Closure $formatter;
    private string $labelFormat;

    private DateInterval $interval;
    private array $expectedStartDates;
    private array $expectedEndDates;
    private array $expectedLabels;
    private array $actualStartDates;
    private array $actualEndDates;

    protected function setUp(): void
    {
        parent::setUp();

        $this->min = null;
        $this->max = null;

        $this->expectedStartDates = [];
        $this->expectedEndDates = [];
        $this->expectedLabels = [];
        $this->actualStartDates = [];
        $this->actualEndDates = [];
    }

    /** @test */
    public function can_generate_today_interval()
    {
        $this->initialStart = Carbon::today();
        $this->initialEnd = Carbon::today()->addHour();
        $this->type = DateIntervals::Today;
        $this->hourly();
    }

    /** @test */
    public function can_generate_this_week_interval()
    {
        $this->initialStart = Carbon::today()->startOfWeek();
        $this->initialEnd = Carbon::today()->startOfWeek()->addDay();
        $this->type = DateIntervals::ThisWeek;
        $this->daily();
    }

    /** @test */
    public function can_generate_this_month_interval()
    {
        $this->initialStart = Carbon::today()->startOfMonth();
        $this->initialEnd = Carbon::today()->startOfMonth()->addDay();
        $this->type = DateIntervals::ThisMonth;
        $this->daily();
    }

    /** @test */
    public function can_generate_this_year_interval()
    {
        $this->initialStart = Carbon::today()->startOfYear();
        $this->initialEnd = Carbon::today()->startOfYear()->addMonth();
        $this->type = DateIntervals::ThisYear;
        $this->monthly();
    }

    /** @test */
    public function can_generate_yesterday_interval()
    {
        $this->initialStart = Carbon::yesterday();
        $this->initialEnd = Carbon::yesterday()->addHour();
        $this->type = DateIntervals::Yesterday;
        $this->hourly();
    }

    /** @test */
    public function can_generate_last_week_interval()
    {
        $this->initialStart = Carbon::today()->subWeek()->startOfWeek();
        $this->initialEnd = Carbon::today()->subWeek()->startOfWeek()->addDay();
        $this->type = DateIntervals::LastWeek;
        $this->daily();
    }

    /** @test */
    public function can_generate_last_month_interval()
    {
        $this->initialStart = Carbon::today()->subMonth()->startOfMonth();
        $this->initialEnd = Carbon::today()->subMonth()->startOfMonth()->addDay();
        $this->type = DateIntervals::LastMonth;
        $this->daily();
    }

    /** @test */
    public function can_generate_last_year_interval()
    {
        $this->initialStart = Carbon::today()->subYear()->startOfYear();
        $this->initialEnd = Carbon::today()->subYear()->startOfYear()->addMonth();
        $this->type = DateIntervals::LastYear;
        $this->monthly();
    }

    /** @test */
    public function can_generate_tomorrow_interval()
    {
        $this->initialStart = Carbon::tomorrow();
        $this->initialEnd = Carbon::tomorrow()->addHour();
        $this->type = DateIntervals::Tomorrow;
        $this->hourly();
    }

    /** @test */
    public function can_generate_next_week_interval()
    {
        $this->initialStart = Carbon::today()->addWeek()->startOfWeek();
        $this->initialEnd = Carbon::today()->addWeek()->startOfWeek()->addDay();
        $this->type = DateIntervals::NextWeek;
        $this->daily();
    }

    /** @test */
    public function can_generate_next_month_interval()
    {
        $this->initialStart = Carbon::today()->addMonth()->startOfMonth();
        $this->initialEnd = Carbon::today()->addMonth()->startOfMonth()->addDay();
        $this->type = DateIntervals::NextMonth;
        $this->daily();
    }

    /** @test */
    public function can_generate_next_year_interval()
    {
        $this->initialStart = Carbon::today()->addYear()->startOfYear();
        $this->initialEnd = Carbon::today()->addYear()->startOfYear()->addMonth();
        $this->type = DateIntervals::NextYear;
        $this->monthly();
    }

    /** @test */
    public function can_identify_hourly_scenario_on_manual_limits()
    {
        $this->min = Carbon::today();
        $this->max = Carbon::today()->addDay();
        $this->initialStart = Carbon::today()->startOfDay();
        $this->initialEnd = Carbon::today()->startOfDay()->addHour();
        $this->type = DateIntervals::Custom;
        $this->hourly();
    }

    /** @test */
    public function can_identify_daily_scenario_on_manual_limits()
    {
        $this->min = Carbon::today();
        $this->max = Carbon::today()->addDays(5);
        $this->initialStart = Carbon::today()->startOfDay();
        $this->initialEnd = Carbon::today()->startOfDay()->addDay();
        $this->type = DateIntervals::Custom;
        $this->daily();
    }

    /** @test */
    public function can_identify_monthly_scenario_on_manual_limits()
    {
        $this->min = Carbon::today();
        $this->max = Carbon::today()->addMonths(2);
        $this->initialStart = Carbon::today();
        $this->initialEnd = Carbon::today()->addMonth();
        $this->type = DateIntervals::Custom;
        $this->monthly();
    }

    /** @test */
    public function can_identify_yearly_scenario_on_manual_limits()
    {
        $this->min = Carbon::today();
        $this->max = Carbon::today()->addYears(2);
        $this->initialStart = Carbon::today();
        $this->initialEnd = Carbon::today()->addYear();
        $this->type = DateIntervals::Custom;
        $this->yearly();
    }

    private function hourly()
    {
        $this->incrementer = fn (Carbon $date) => $date->addHour();
        $this->formatter = fn (Carbon $date) => $date->startOfHour();
        $this->labelFormat = 'H';
        $this->handle();
    }

    private function daily()
    {
        $this->incrementer = fn (Carbon $date) => $date->addDay();
        $this->formatter = fn (Carbon $date) => $date->startOfDay();
        $this->labelFormat = Config::get('enso.config.dateFormat');
        $this->handle();
    }

    private function monthly()
    {
        $this->incrementer = fn (Carbon $date) => $date->addMonth();
        $this->formatter = fn (Carbon $date) => $date->startOfMonth();
        $this->labelFormat = 'M-y';
        $this->handle();
    }

    private function yearly()
    {
        $this->incrementer = fn (Carbon $date) => $date->addYear();
        $this->formatter = fn (Carbon $date) => $date->startOfYear();
        $this->labelFormat = 'Y';
        $this->handle();
    }

    private function handle()
    {
        $this->init()
            ->iterate($this->incrementer)
            ->assert();
    }

    private function init()
    {
        $this->interval = new DateInterval($this->type, $this->min, $this->max);
        $formatter = $this->formatter;
        $formatter($this->initialStart);
        $formatter($this->initialEnd);

        return $this;
    }

    private function iterate(Closure $incrementer)
    {
        while ($this->interval->valid()) {
            $this->expectedStartDates[] = $this->initialStart->toString();
            $this->expectedEndDates[] = $this->initialEnd->toString();
            $this->expectedLabels[] = $this->initialStart->format($this->labelFormat);
            $this->actualStartDates[] = $this->interval->start()->toString();
            $this->actualEndDates[] = $this->interval->end()->toString();
            $incrementer($this->initialStart);
            $incrementer($this->initialEnd);
            $this->interval->next();
        }

        return $this;
    }

    private function assert()
    {
        $this->assertEqualsCanonicalizing($this->expectedStartDates, $this->actualStartDates);
        $this->assertEqualsCanonicalizing($this->expectedEndDates, $this->actualEndDates);
        $this->assertEqualsCanonicalizing($this->expectedLabels, $this->interval->labels());
    }
}
