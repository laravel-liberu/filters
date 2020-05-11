<?php

namespace LaravelEnso\Filters\App\Enums;

use LaravelEnso\Enums\App\Services\Enum;

class TimeSegments extends Enum
{
    public const Hourly = 1;
    public const Daily = 2;
    public const Monthly = 3;
    public const Yearly = 4;
}
