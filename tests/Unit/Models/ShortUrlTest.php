<?php

use Adereksisusanto\FilamentShlink\Models\ShortUrl;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrl as ShlinkShortUrl;

it('can be created from ShlinkShortUrl DTO', function () {
    $shlinkUrl = ShlinkShortUrl::fromArray([
        'shortCode' => 'abc123',
        'shortUrl' => 'https://s.test/abc123',
        'longUrl' => 'https://example.com/long-url',
        'dateCreated' => '2024-01-15T10:00:00+00:00',
        'domain' => 's.test',
        'title' => 'Example Title',
        'crawlable' => true,
        'forwardQuery' => true,
        'hasRedirectRules' => false,
        'tags' => ['laravel', 'filament'],
        'meta' => ['maxVisits' => 100],
        'visitsSummary' => ['total' => 50, 'nonBots' => 45, 'bots' => 5],
    ]);

    $model = ShortUrl::fromShlinkShortUrl($shlinkUrl);

    expect($model)
        ->toBeInstanceOf(ShortUrl::class)
        ->and($model->shortCode)->toBe('abc123')
        ->and($model->shortUrl)->toBe('https://s.test/abc123')
        ->and($model->longUrl)->toBe('https://example.com/long-url')
        ->and($model->domain)->toBe('s.test')
        ->and($model->title)->toBe('Example Title')
        ->and($model->crawlable)->toBeTrue()
        ->and($model->forwardQuery)->toBeTrue()
        ->and($model->hasRedirectRules)->toBeFalse()
        ->and($model->tags)->toBe(['laravel', 'filament'])
        ->and($model->meta)->not->toBeNull()
        ->and($model->visitsSummary)->not->toBeNull();
});

it('uses shortCode as the key name', function () {
    $model = new ShortUrl(['shortCode' => 'xyz789']);

    expect($model->getKeyName())->toBe('shortCode')
        ->and($model->getKey())->toBe('xyz789');
});

it('can generate a ShortUrlIdentifier', function () {
    $shlinkUrl = ShlinkShortUrl::fromArray([
        'shortCode' => 'abc123',
        'shortUrl' => 'https://s.test/abc123',
        'longUrl' => 'https://example.com/long-url',
        'dateCreated' => '2024-01-15T10:00:00+00:00',
        'domain' => 's.test',
        'tags' => [],
        'meta' => [],
        'visitsSummary' => [],
    ]);

    $model = ShortUrl::fromShlinkShortUrl($shlinkUrl);
    $identifier = $model->identifier();

    expect($identifier->shortCode)->toBe('abc123');
});

it('handles nullable fields correctly', function () {
    $shlinkUrl = ShlinkShortUrl::fromArray([
        'shortCode' => 'null-test',
        'shortUrl' => 'https://s.test/null-test',
        'longUrl' => 'https://example.com',
        'dateCreated' => '2024-01-15T10:00:00+00:00',
        'domain' => null,
        'title' => null,
        'crawlable' => false,
        'forwardQuery' => false,
        'hasRedirectRules' => null,
        'tags' => [],
        'meta' => [],
        'visitsSummary' => [],
    ]);

    $model = ShortUrl::fromShlinkShortUrl($shlinkUrl);

    expect($model->domain)->toBeNull()
        ->and($model->title)->toBeNull()
        ->and($model->hasRedirectRules)->toBeNull()
        ->and($model->crawlable)->toBeFalse()
        ->and($model->forwardQuery)->toBeFalse();
});

it('has timestamps disabled', function () {
    $model = new ShortUrl;

    expect($model->timestamps)->toBeFalse();
});

it('uses the correct table name', function () {
    $model = new ShortUrl;

    expect($model->getTable())->toBe('shlink_short_urls');
});
