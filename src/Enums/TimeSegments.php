<?php

namespace LaravelEnso\Filters\Enums;

use LaravelEnso\Enums\Services\Enum;

class TimeSegments extends Enum
{
    public const Hourly = 1;
    public const Daily = 2;
    public const Monthly = 3;
    public const Yearly = 4;
}
