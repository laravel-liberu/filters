<?php

namespace LaravelLiberu\Filters\Enums;

use LaravelLiberu\Enums\Services\Enum;

class Operations extends Enum
{
    final public const None = 0;
    final public const Decrease = -1;
    final public const Increase = 1;
}
