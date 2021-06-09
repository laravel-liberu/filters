<?php

namespace LaravelEnso\Filters\Enums;

use LaravelEnso\Enums\Services\Enum;
use LaravelEnso\Filters\Exceptions\ComparisonOperator;

class ComparisonOperators extends Enum
{
    public const Like = 'LIKE';
    public const ILike = 'ILIKE';
    public const Equal = '=';
    public const Is = 'IS';
    public const IsNot = 'IS NOT';
    public const NotLike = 'NOT LIKE';
    public const NotILike = 'NOT ILIKE';

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
