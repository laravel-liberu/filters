<?php

namespace LaravelEnso\Filters\Enums;

use LaravelEnso\Enums\Services\Enum;

class SearchModes extends Enum
{
    public const ExactMatch = 'exactMatch';
    public const Full = 'full';
    public const StartsWith = 'startsWith';
    public const EndsWith = 'endsWith';
    public const DoesntContain = 'doesntContain';
    public const Algolia = 'searchProvider';
}
