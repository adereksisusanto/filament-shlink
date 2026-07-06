<?php

namespace Adereksisusanto\FilamentShlink\Filament\Widgets;

use Adereksisusanto\FilamentShlink\FilamentShlink;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class VisitsOverviewWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $shlink = app(FilamentShlink::class);

        if (! $shlink->isConfigured()) {
            return [
                Stat::make(__('filament-shlink::filament-shlink.visits_overview.not_configured'), '—'),
            ];
        }

        try {
            $overview = $shlink->getVisitsOverview();
            $shortUrls = $shlink->listShortUrls();
            $tags = $shlink->listTagsWithStats();

            return [
                Stat::make(
                    __('filament-shlink::filament-shlink.visits_overview.total_visits'),
                    number_format($overview->total),
                ),
                Stat::make(
                    __('filament-shlink::filament-shlink.visits_overview.total_short_urls'),
                    number_format($shortUrls->pagination->totalItems),
                ),
                Stat::make(
                    __('filament-shlink::filament-shlink.visits_overview.total_tags'),
                    number_format(count($tags)),
                ),
            ];
        } catch (\Throwable) {
            return [
                Stat::make(__('filament-shlink::filament-shlink.visits_overview.error'), '—'),
            ];
        }
    }
}
