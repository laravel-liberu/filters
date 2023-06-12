<?php

namespace LaravelEnso\Filters\Enums;

use LaravelEnso\Enums\Services\Enum;

class TimeSegments extends Enum
{
    final public const Hourly = 1;
    final public const Daily = 2;
    final public const Monthly = 3;
    final public const Yearly = 4;
}
