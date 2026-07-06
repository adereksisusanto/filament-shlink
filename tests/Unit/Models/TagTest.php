<?php

use Adereksisusanto\FilamentShlink\Models\Tag;
use Shlinkio\Shlink\SDK\Tags\Model\TagWithStats;

it('can be created from TagWithStats DTO', function () {
    $tagWithStats = TagWithStats::fromArray([
        'tag' => 'laravel',
        'shortUrlsCount' => 10,
        'visitsSummary' => ['total' => 100, 'nonBots' => 80, 'bots' => 20],
    ]);

    $model = Tag::fromShlinkTagWithStats($tagWithStats);

    expect($model)
        ->toBeInstanceOf(Tag::class)
        ->and($model->name)->toBe('laravel')
        ->and($model->shortUrlsCount)->toBe(10)
        ->and($model->totalVisits)->toBe(100);
});

it('uses name as the key', function () {
    $model = new Tag(['name' => 'filament']);

    expect($model->getKeyName())->toBe('name')
        ->and($model->getKey())->toBe('filament');
});

it('has timestamps disabled', function () {
    $model = new Tag;

    expect($model->timestamps)->toBeFalse();
});

it('uses the correct table name', function () {
    $model = new Tag;

    expect($model->getTable())->toBe('shlink_tags');
});
