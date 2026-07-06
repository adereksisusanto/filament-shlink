<?php

namespace Adereksisusanto\FilamentShlink\Filament\Resources\ShortUrlResource\Pages;

use Adereksisusanto\FilamentShlink\Filament\Resources\ShortUrlResource;
use Adereksisusanto\FilamentShlink\Filament\Widgets\VisitsOverviewWidget;
use Adereksisusanto\FilamentShlink\FilamentShlink;
use Adereksisusanto\FilamentShlink\Models\ShortUrl;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrlIdentifier;

class ListShortUrls extends ListRecords
{
    protected static string $resource = ShortUrlResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            VisitsOverviewWidget::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('create')
                ->label(__('filament-shlink::filament-shlink.create_short_url'))
                ->icon('heroicon-o-plus')
                ->url(ShortUrlResource::getUrl('create')),
            Action::make('refresh')
                ->label(__('filament-shlink::filament-shlink.refresh'))
                ->icon('heroicon-o-arrow-path')
                ->action(fn () => $this->resetTable()),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->records(fn (): Collection => $this->getTableRecords())
            ->columns([
                TextColumn::make('shortUrl')
                    ->label(__('filament-shlink::filament-shlink.short_url'))
                    ->searchable()
                    ->copyable()
                    ->copyMessage('URL copied'),
                TextColumn::make('longUrl')
                    ->label(__('filament-shlink::filament-shlink.long_url'))
                    ->limit(40)
                    ->searchable(),
                TextColumn::make('title')
                    ->label(__('filament-shlink::filament-shlink.title')),
                TextColumn::make('shortCode')
                    ->label(__('filament-shlink::filament-shlink.short_code')),
                TextColumn::make('tags')
                    ->label(__('filament-shlink::filament-shlink.tags'))
                    ->badge(),
                TextColumn::make('visits')
                    ->label(__('filament-shlink::filament-shlink.visits')),
                TextColumn::make('dateCreated')
                    ->label(__('filament-shlink::filament-shlink.date_created'))
                    ->dateTime(),
            ])
            ->recordActions([
                Action::make('open')
                    ->label(__('filament-shlink::filament-shlink.open'))
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn (ShortUrl $record): string => $record->shortUrl)
                    ->openUrlInNewTab(),
                EditAction::make()
                    ->url(fn (ShortUrl $record): string => ShortUrlResource::getUrl('edit', ['record' => $record->shortCode])),
                DeleteAction::make()
                    ->action(function (ShortUrl $record) {
                        try {
                            app(FilamentShlink::class)->deleteShortUrl(
                                ShortUrlIdentifier::fromShortCode($record->shortCode),
                            );
                            Notification::make()->title('Deleted')->success()->send();
                        } catch (\Throwable $e) {
                            Notification::make()->title($e->getMessage())->danger()->send();
                        }
                        $this->resetTable();
                    }),
            ]);
    }

    public function getTableRecords(): Collection
    {
        try {
            $service = app(FilamentShlink::class);
            if (! $service->isConfigured()) {
                return collect();
            }

            $shortUrls = $service->listShortUrls();
            $records = [];
            foreach ($shortUrls as $sdkUrl) {
                $model = ShortUrl::fromShlinkShortUrl($sdkUrl);
                $records[$model->getKey()] = $model;
            }

            return collect($records);
        } catch (\Throwable $e) {
            Notification::make()->title($e->getMessage())->danger()->send();

            return collect();
        }
    }
}
