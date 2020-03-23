<?php

namespace LaravelEnso\Filters\App\DTOs;

use Carbon\Carbon;

class Segment
{
    private Carbon $start;
    private Carbon $end;

    public function __construct(Carbon $start, Carbon $end)
    {
        $this->start = $start;
        $this->end = $end;
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
