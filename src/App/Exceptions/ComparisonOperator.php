<?php

namespace LaravelEnso\Filters\App\Exceptions;

use InvalidArgumentException;

class ComparisonOperator extends InvalidArgumentException
{
    public static function unknown()
    {
        return new static(__('Unknown comparison operator provided'));
    }

    public static function notInversable(string $operator)
    {
        return new static(__('The provided operator ":operator" is not inversable', ['operator' => $operator]));
    }
}
