<?php

namespace LaravelEnso\Select\App\Exceptions;

use InvalidArgumentException;

class ComparisonOperator extends InvalidArgumentException
{
    public static function unknown()
    {
        return new static(__('Unknown comparison operator provided'));
    }
}
