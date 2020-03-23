<?php

use Carbon\Carbon;
use LaravelEnso\Filters\App\Enums\DateIntervals;
use LaravelEnso\Filters\App\Exceptions\InvalidArgument;
use LaravelEnso\Filters\App\Services\DateInterval;
use Tests\TestCase;

class DateIntervalValidatorTest extends TestCase
{
    /** @test */
    public function validates_type()
    {
        $type = 'unknown_type';

        $this->expectException(InvalidArgument::class);
        $this->expectExceptionMessage(InvalidArgument::type($type)->getMessage());

        new DateInterval($type);
    }

    /** @test */
    public function validates_limit_requirement_on_manual_interval()
    {
        $this->expectException(InvalidArgument::class);
        $this->expectExceptionMessage(InvalidArgument::limit()->getMessage());

        new DateInterval(DateIntervals::Custom);
    }

    /** @test */
    public function validates_interval_on_manual_interval()
    {
        $this->expectException(InvalidArgument::class);
        $this->expectExceptionMessage(InvalidArgument::interval()->getMessage());

        new DateInterval(DateIntervals::Custom, Carbon::tomorrow(), Carbon::today());
    }
}
