<?php

namespace LaravelEnso\Filters\Enums;

use LaravelEnso\Enums\Services\Enum;

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

    public static function isManual(string $type): bool
    {
        return in_array($type, [self::Custom, self::All]);
    }
}
