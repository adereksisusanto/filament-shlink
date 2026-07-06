<?php

use Adereksisusanto\FilamentShlink\FilamentShlink;
use Adereksisusanto\FilamentShlink\FilamentShlinkPlugin;
use Shlinkio\Shlink\SDK\ShlinkClient;

beforeEach(function () {
    $this->service = new FilamentShlink;
    clearConfig();
});

afterEach(function () {
    clearConfig();
});

function clearConfig(): void
{
    config()->set('filament-shlink.server_url', '');
    config()->set('filament-shlink.api_key', '');
}

it('is not configured when config is empty', function () {
    expect($this->service->isConfigured())->toBeFalse();
});

it('is not configured when only server_url is set', function () {
    config()->set('filament-shlink.server_url', 'https://s.test');

    expect($this->service->isConfigured())->toBeFalse();
});

it('is not configured when only api_key is set', function () {
    config()->set('filament-shlink.api_key', 'some-key');

    expect($this->service->isConfigured())->toBeFalse();
});

it('is configured when both server_url and api_key are set', function () {
    config()->set('filament-shlink.server_url', 'https://s.test');
    config()->set('filament-shlink.api_key', 'valid-api-key');

    expect($this->service->isConfigured())->toBeTrue();
});

it('setConfig creates a ShlinkClient', function () {
    $this->service->setConfig('https://s.test', 'api-key');

    $client = $this->service->client();

    expect($client)->toBeInstanceOf(ShlinkClient::class);
});

it('setConfig returns the same client instance on subsequent calls', function () {
    $this->service->setConfig('https://s.test', 'api-key');

    $client1 = $this->service->client();
    $client2 = $this->service->client();

    expect($client1)->toBe($client2);
});

it('setConfig replaces the previous client', function () {
    $this->service->setConfig('https://s.test', 'key1');
    $client1 = $this->service->client();

    $this->service->setConfig('https://s2.test', 'key2');
    $client2 = $this->service->client();

    expect($client1)->not->toBe($client2);
});

it('plugin modal defaults to false', function () {
    $plugin = FilamentShlinkPlugin::make();

    expect($plugin->isModal())->toBeFalse();
});

it('plugin modal can be set to true', function () {
    $plugin = FilamentShlinkPlugin::make()->modal(true);

    expect($plugin->isModal())->toBeTrue();
});

it('plugin modal can be toggled off', function () {
    $plugin = FilamentShlinkPlugin::make()->modal(true);
    expect($plugin->isModal())->toBeTrue();

    $plugin->modal(false);
    expect($plugin->isModal())->toBeFalse();
});

it('plugin modal returns self for chaining', function () {
    $plugin = FilamentShlinkPlugin::make();

    $result = $plugin->modal(true);

    expect($result)->toBe($plugin);
});
