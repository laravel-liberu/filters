<?php

namespace LaravelEnso\Filters\App\Enums;

use LaravelEnso\Enums\App\Services\Enum;

class Operations extends Enum
{
    public const None = 0;
    public const Decrease = -1;
    public const Increase = 1;
}
