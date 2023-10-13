<?php

namespace LaravelLiberu\Filters\Enums;

use LaravelLiberu\Enums\Services\Enum;

class SearchModes extends Enum
{
    final public const ExactMatch = 'exactMatch';
    final public const Full = 'full';
    final public const StartsWith = 'startsWith';
    final public const EndsWith = 'endsWith';
    final public const DoesntContain = 'doesntContain';
    final public const Algolia = 'searchProvider';
}
