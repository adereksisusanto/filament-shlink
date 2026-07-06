<?php

use Adereksisusanto\FilamentShlink\Filament\Widgets\VisitsOverviewWidget;

function getWidgetStats(VisitsOverviewWidget $widget): array
{
    $reflection = new ReflectionMethod($widget, 'getStats');
    $reflection->setAccessible(true);

    return $reflection->invoke($widget);
}

beforeEach(function () {
    config()->set('filament-shlink.server_url', '');
    config()->set('filament-shlink.api_key', '');
});

it('returns a not-configured stat when shlink is not configured', function () {
    $widget = new VisitsOverviewWidget;
    $stats = getWidgetStats($widget);

    expect($stats)->toHaveCount(1)
        ->and($stats[0]->getLabel())->toBe('Not Configured');
});

it('catches error and returns error stat when api call fails', function () {
    config()->set('filament-shlink.server_url', 'https://invalid.test');
    config()->set('filament-shlink.api_key', 'invalid-key');

    $widget = new VisitsOverviewWidget;
    $stats = getWidgetStats($widget);

    expect($stats)->toHaveCount(1)
        ->and($stats[0]->getLabel())->toBe('Error loading data');
});
