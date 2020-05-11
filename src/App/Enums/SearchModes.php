<?php

namespace LaravelEnso\Filters\App\Enums;

use LaravelEnso\Enums\App\Services\Enum;

class SearchModes extends Enum
{
    public const ExactMatch = 'exactMatch';
    public const Full = 'full';
    public const StartsWith = 'startsWith';
    public const EndsWith = 'endsWith';
    public const DoesntContain = 'doesntContain';
    public const Algolia = 'algolia';
}
