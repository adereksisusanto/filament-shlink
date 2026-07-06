<?php

use Adereksisusanto\FilamentShlink\FilamentShlink;
use Adereksisusanto\FilamentShlink\FilamentShlinkServiceProvider;

it('registers the FilamentShlink singleton in the container', function () {
    $instance = app(FilamentShlink::class);

    expect($instance)->toBeInstanceOf(FilamentShlink::class);
});

it('returns the same instance from the container', function () {
    $instance1 = app(FilamentShlink::class);
    $instance2 = app(FilamentShlink::class);

    expect($instance1)->toBe($instance2);
});

it('loads config file', function () {
    $config = config('filament-shlink');

    expect($config)->toBeArray()
        ->toHaveKeys(['server_url', 'api_key']);
});

it('loads translations', function () {
    $trans = trans('filament-shlink::filament-shlink.short_urls');

    expect($trans)->toBeString()->not->toBeEmpty();
});

it('loads views', function () {
    $views = view()->getFinder()->getHints();

    expect($views)->toHaveKey('filament-shlink');
});

it('has the correct package name', function () {
    $provider = app()->getProvider(FilamentShlinkServiceProvider::class);

    expect($provider::$name)->toBe('filament-shlink');
});
