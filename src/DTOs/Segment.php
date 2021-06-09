<?php

namespace LaravelEnso\Filters\DTOs;

use Carbon\Carbon;

class Segment
{
    public function __construct(
        private Carbon $start,
        private Carbon $end
    ) {
    }

    public function start(): Carbon
    {
        return $this->start;
    }

    public function end(): Carbon
    {
        return $this->end;
    }
}
