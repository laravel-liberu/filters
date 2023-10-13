<?php

namespace LaravelLiberu\Filters\Exceptions;

use InvalidArgumentException;

class Interval extends InvalidArgumentException
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
