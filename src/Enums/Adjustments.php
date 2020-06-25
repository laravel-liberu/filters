<?php

namespace LaravelEnso\Filters\Enums;

use LaravelEnso\Enums\Services\Enum;

class Adjustments extends Enum
{
    protected static array $data = [
        Intervals::Today => Operations::None,
        Intervals::ThisWeek => Operations::None,
        Intervals::ThisMonth => Operations::None,
        Intervals::ThisYear => Operations::None,
        Intervals::Yesterday => Operations::Decrease,
        Intervals::LastWeek => Operations::Decrease,
        Intervals::LastMonth => Operations::Decrease,
        Intervals::LastYear => Operations::Decrease,
        Intervals::Tomorrow => Operations::Increase,
        Intervals::NextWeek => Operations::Increase,
        Intervals::NextMonth => Operations::Increase,
        Intervals::NextYear => Operations::Increase,
    ];
}
