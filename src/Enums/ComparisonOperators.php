<?php

namespace LaravelEnso\Filters\Enums;

use LaravelEnso\Enums\Services\Enum;
use LaravelEnso\Filters\Exceptions\ComparisonOperator;

class ComparisonOperators extends Enum
{
    final public const Like = 'LIKE';
    final public const ILike = 'ILIKE';
    final public const Equal = '=';
    final public const Is = 'IS';
    final public const IsNot = 'IS NOT';
    final public const NotLike = 'NOT LIKE';
    final public const NotILike = 'NOT ILIKE';

    public static function invert(string $operator): string
    {
        return match ($operator) {
            self::Like => self::NotLike,
            self::ILike => self::NotILike,
            self::Is => self::IsNot,
            default => throw ComparisonOperator::notInversable($operator)
        };
    }
}
