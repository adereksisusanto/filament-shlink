<?php

namespace Adereksisusanto\FilamentShlink;

use Adereksisusanto\FilamentShlink\Enums\ModalType;
use Adereksisusanto\FilamentShlink\Filament\Pages\ShlinkSettings;
use Adereksisusanto\FilamentShlink\Filament\Resources\ShortUrlResource;
use Adereksisusanto\FilamentShlink\Filament\Resources\TagResource;
use Adereksisusanto\FilamentShlink\Models\ShlinkConfig;
use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\SlideOverPosition;
use Filament\Support\Enums\Width;

class FilamentShlinkPlugin implements Plugin
{
    protected bool $modal = false;

    protected ?ModalType $modalType = null;

    protected ?SlideOverPosition $modalPosition = null;

    protected ?Width $modalWidth = null;

    protected ?Alignment $modalAlignment = null;

    protected bool $multiClient = false;

    protected string $tablePrefix = 'fs';

    public function getId(): string
    {
        return 'filament-shlink';
    }

    public function register(Panel $panel): void
    {
        config(['filament-shlink.multi_client' => $this->multiClient]);

        if ($this->multiClient) {
            ShlinkConfig::setTablePrefix($this->tablePrefix);
            config(['filament-shlink.table_prefix' => $this->tablePrefix]);
        }

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

    public function modal(
        bool $enabled = true,
        ?ModalType $type = null,
        ?SlideOverPosition $position = null,
        ?Width $width = null,
        ?Alignment $alignment = null,
    ): static {
        $this->modal = $enabled;
        $this->modalType = $type;
        $this->modalPosition = $position;
        $this->modalWidth = $width;
        $this->modalAlignment = $alignment;

        return $this;
    }

    public function isModal(): bool
    {
        return $this->modal;
    }

    public function getModalType(): ?ModalType
    {
        return $this->modalType;
    }

    public function getModalPosition(): ?SlideOverPosition
    {
        return $this->modalPosition;
    }

    public function getModalWidth(): ?Width
    {
        return $this->modalWidth;
    }

    public function getModalAlignment(): ?Alignment
    {
        return $this->modalAlignment;
    }

    public function multiClient(bool $enabled = true, string $tablePrefix = 'fs'): static
    {
        $this->multiClient = $enabled;
        $this->tablePrefix = $tablePrefix;

        return $this;
    }

    public function isMultiClient(): bool
    {
        return $this->multiClient;
    }

    public function getTablePrefix(): string
    {
        return $this->tablePrefix;
    }
}
