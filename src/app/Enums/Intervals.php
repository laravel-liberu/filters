<?php

namespace LaravelEnso\Filters\App\Enums;

use LaravelEnso\Enums\App\Services\Enum;

class Intervals extends Enum
{
    public const Today = 'today';
    public const ThisWeek = 'thisWeek';
    public const ThisMonth = 'thisMonth';
    public const ThisYear = 'thisYear';
    public const Yesterday = 'yesterday';
    public const LastWeek = 'lastWeek';
    public const LastMonth = 'lastMonth';
    public const LastYear = 'lastYear';
    public const Tomorrow = 'tomorrow';
    public const NextWeek = 'nextWeek';
    public const NextMonth = 'nextMonth';
    public const NextYear = 'nextYear';
    public const Custom = 'custom';
    public const All = 'all';

    private static array $adjustments = [
        self::Today => 0,
        self::ThisWeek => 0,
        self::ThisMonth => 0,
        self::ThisYear => 0,
        self::Yesterday => -1,
        self::LastWeek => -1,
        self::LastMonth => -1,
        self::LastYear => -1,
        self::Tomorrow => 1,
        self::NextWeek => 1,
        self::NextMonth => 1,
        self::NextYear => 1,
    ];

    public static function adjustment(string $type): ?int
    {
        return self::$adjustments[$type] ?? null;
    }
}
