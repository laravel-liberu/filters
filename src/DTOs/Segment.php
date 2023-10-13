<?php

namespace LaravelLiberu\Filters\DTOs;

use Carbon\Carbon;

class Segment
{
    public function __construct(
        private readonly Carbon $start,
        private readonly Carbon $end
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
