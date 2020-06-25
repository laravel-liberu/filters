<?php

namespace LaravelEnso\Filters\Exceptions;

use InvalidArgumentException;

class SearchMode extends InvalidArgumentException
{
    public static function unknown()
    {
        return new static(__('Unknown search mode provided'));
    }
}
