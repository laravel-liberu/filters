<?php

use Carbon\Carbon;
use LaravelLiberu\Filters\Enums\Intervals;
use LaravelLiberu\Filters\Exceptions\Interval as Exception;
use LaravelLiberu\Filters\Services\Interval;
use Tests\TestCase;

class IntervalValidatorTest extends TestCase
{
    /** @test */
    public function validates_type()
    {
        $type = 'unknown_type';

        $this->expectException(Exception::class);
        $this->expectExceptionMessage(Exception::type($type)->getMessage());

        new Interval($type);
    }

    /** @test */
    public function validates_limit_requirement_on_manual_interval()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage(Exception::limit()->getMessage());

        new Interval(Intervals::Custom);
    }

    /** @test */
    public function validates_interval_on_manual_interval()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage(Exception::interval()->getMessage());

        new Interval(Intervals::Custom, Carbon::tomorrow(), Carbon::today());
    }
}
