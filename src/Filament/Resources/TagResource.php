<?php

namespace Adereksisusanto\FilamentShlink\Filament\Resources;

use Adereksisusanto\FilamentShlink\Filament\Resources\TagResource\Pages;
use Adereksisusanto\FilamentShlink\Models\Tag;
use Filament\Resources\Resource;

class TagResource extends Resource
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-tag';

    protected static ?string $slug = 'shlink-tags';

    protected static bool $isGloballySearchable = false;

    public static function getModel(): string
    {
        return Tag::class;
    }

    public static function getNavigationLabel(): string
    {
        return __('filament-shlink::filament-shlink.tags');
    }

    public static function getPluralLabel(): string
    {
        return __('filament-shlink::filament-shlink.tags');
    }

    public static function getLabel(): string
    {
        return __('filament-shlink::filament-shlink.tag');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTags::route('/'),
        ];
    }
}
