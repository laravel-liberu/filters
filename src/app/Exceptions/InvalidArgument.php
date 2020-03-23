<?php

namespace LaravelEnso\Filters\App\Exceptions;

use InvalidArgumentException;

class InvalidArgument extends InvalidArgumentException
{
    public static function type(string $type)
    {
        return new static("Unknown interval type: {$type}");
    }

    public static function limit()
    {
        return new static('At least on limit is required, min or max');
    }

    public static function interval()
    {
        return new static('Min cannot be after max');
    }
}
