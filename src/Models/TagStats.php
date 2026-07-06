<?php

namespace Adereksisusanto\FilamentShlink\Models;

use Shlinkio\Shlink\SDK\Tags\Model\TagWithStats;

class TagStats
{
    public function __construct(
        public readonly int $shortUrlsCount = 0,
        public readonly int $totalVisits = 0,
    ) {}

    public static function fromShlinkTagWithStats(TagWithStats $tagStats): self
    {
        return new self(
            shortUrlsCount: $tagStats->shortUrlsCount,
            totalVisits: $tagStats->totalVisits,
        );
    }
}
