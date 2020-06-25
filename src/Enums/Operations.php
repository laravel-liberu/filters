<?php

namespace LaravelEnso\Filters\Enums;

use LaravelEnso\Enums\Services\Enum;

class Operations extends Enum
{
    public const None = 0;
    public const Decrease = -1;
    public const Increase = 1;
}
