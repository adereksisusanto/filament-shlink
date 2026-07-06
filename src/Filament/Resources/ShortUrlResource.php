<?php

namespace Adereksisusanto\FilamentShlink\Filament\Resources;

use Adereksisusanto\FilamentShlink\Filament\Resources\ShortUrlResource\Pages;
use Adereksisusanto\FilamentShlink\Models\ShortUrl;
use Filament\Resources\Resource;

class ShortUrlResource extends Resource
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-link';

    protected static ?string $slug = 'shlink-short-urls';

    protected static bool $isGloballySearchable = false;

    public static function getModel(): string
    {
        return ShortUrl::class;
    }

    public static function getNavigationLabel(): string
    {
        return __('filament-shlink::filament-shlink.short_urls');
    }

    public static function getPluralLabel(): string
    {
        return __('filament-shlink::filament-shlink.short_urls');
    }

    public static function getLabel(): string
    {
        return __('filament-shlink::filament-shlink.short_url');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListShortUrls::route('/'),
            'create' => Pages\CreateShortUrl::route('/create'),
            'edit' => Pages\EditShortUrl::route('/{record}/edit'),
        ];
    }
}
