<?php

namespace Adereksisusanto\FilamentShlink;

use Adereksisusanto\FilamentShlink\Filament\Pages\ShlinkSettings;
use Adereksisusanto\FilamentShlink\Filament\Resources\ShortUrlResource;
use Adereksisusanto\FilamentShlink\Filament\Resources\TagResource;
use Filament\Contracts\Plugin;
use Filament\Panel;

class FilamentShlinkPlugin implements Plugin
{
    protected bool $modal = false;

    public function getId(): string
    {
        return 'filament-shlink';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->resources([
                ShortUrlResource::class,
                TagResource::class,
            ])
            ->pages([
                ShlinkSettings::class,
            ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }

    public function modal(bool $modal = true): static
    {
        $this->modal = $modal;

        return $this;
    }

    public function isModal(): bool
    {
        return $this->modal;
    }
}
