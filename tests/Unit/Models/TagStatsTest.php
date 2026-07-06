<?php

use Adereksisusanto\FilamentShlink\Models\TagStats;
use Shlinkio\Shlink\SDK\Tags\Model\TagWithStats;

it('can be created from TagWithStats DTO', function () {
    $tagWithStats = TagWithStats::fromArray([
        'tag' => 'laravel',
        'shortUrlsCount' => 10,
        'visitsSummary' => ['total' => 100, 'nonBots' => 80, 'bots' => 20],
    ]);

    $stats = TagStats::fromShlinkTagWithStats($tagWithStats);

    expect($stats)
        ->toBeInstanceOf(TagStats::class)
        ->and($stats->shortUrlsCount)->toBe(10)
        ->and($stats->totalVisits)->toBe(100);
});

it('defaults to zero values when visits summary is empty', function () {
    $tagWithStats = TagWithStats::fromArray([
        'tag' => 'empty',
        'shortUrlsCount' => 0,
        'visitsSummary' => [],
    ]);

    $stats = TagStats::fromShlinkTagWithStats($tagWithStats);

    expect($stats->shortUrlsCount)->toBe(0)
        ->and($stats->totalVisits)->toBe(0);
});
