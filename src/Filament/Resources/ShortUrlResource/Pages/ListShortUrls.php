<?php

namespace Adereksisusanto\FilamentShlink\Filament\Resources\ShortUrlResource\Pages;

use Adereksisusanto\FilamentShlink\Filament\Resources\ShortUrlResource;
use Adereksisusanto\FilamentShlink\Filament\Widgets\VisitsOverviewWidget;
use Adereksisusanto\FilamentShlink\FilamentShlink;
use Adereksisusanto\FilamentShlink\FilamentShlinkPlugin;
use Adereksisusanto\FilamentShlink\Models\ShortUrl;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrlCreation;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrlEdition;
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
        $actions = [
            Action::make('refresh')
                ->label(__('filament-shlink::filament-shlink.refresh'))
                ->icon('heroicon-o-arrow-path')
                ->action(fn () => $this->resetTable()),
        ];

        if ($this->isModalMode()) {
            array_unshift($actions, $this->getCreateModalAction());
        } else {
            array_unshift(
                $actions,
                Action::make('create')
                    ->label(__('filament-shlink::filament-shlink.create_short_url'))
                    ->icon('heroicon-o-plus')
                    ->url(ShortUrlResource::getUrl('create')),
            );
        }

        return $actions;
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
                $this->isModalMode()
                    ? $this->getEditModalAction()
                    : EditAction::make()
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

    protected function isModalMode(): bool
    {
        return FilamentShlinkPlugin::get()->isModal();
    }

    protected function getCreateModalAction(): Action
    {
        return Action::make('create')
            ->label(__('filament-shlink::filament-shlink.create_short_url'))
            ->icon('heroicon-o-plus')
            ->form([
                TextInput::make('long_url')
                    ->label(__('filament-shlink::filament-shlink.long_url'))
                    ->required()
                    ->url()
                    ->columnSpanFull(),
                TextInput::make('custom_slug')
                    ->label(__('filament-shlink::filament-shlink.custom_slug')),
                TextInput::make('title')
                    ->label(__('filament-shlink::filament-shlink.title')),
                TextInput::make('domain')
                    ->label(__('filament-shlink::filament-shlink.domain')),
                TextInput::make('path_prefix')
                    ->label(__('filament-shlink::filament-shlink.path_prefix')),
                Select::make('short_code_length')
                    ->label(__('filament-shlink::filament-shlink.short_code_length'))
                    ->options(array_combine(range(4, 10), range(4, 10))),
                TagsInput::make('tags')
                    ->label(__('filament-shlink::filament-shlink.tags')),
                DateTimePicker::make('valid_since')
                    ->label(__('filament-shlink::filament-shlink.valid_since')),
                DateTimePicker::make('valid_until')
                    ->label(__('filament-shlink::filament-shlink.valid_until')),
                TextInput::make('max_visits')
                    ->label(__('filament-shlink::filament-shlink.max_visits'))
                    ->numeric(),
                Toggle::make('crawlable')
                    ->label(__('filament-shlink::filament-shlink.crawlable')),
                Toggle::make('forward_query')
                    ->label(__('filament-shlink::filament-shlink.forward_query'))
                    ->default(true),
                Toggle::make('find_if_exists')
                    ->label(__('filament-shlink::filament-shlink.find_if_exists')),
            ])
            ->action(function (array $data) {
                $service = app(FilamentShlink::class);
                $creation = ShortUrlCreation::forLongUrl($data['long_url']);

                if (! empty($data['custom_slug'])) {
                    $creation = $creation->withCustomSlug($data['custom_slug']);
                }
                if (! empty($data['title'])) {
                    $creation = $creation->withTitle($data['title']);
                }
                if (! empty($data['domain'])) {
                    $creation = $creation->forDomain($data['domain']);
                }
                if (! empty($data['path_prefix'])) {
                    $creation = $creation->withPathPrefix($data['path_prefix']);
                }
                if (! empty($data['short_code_length'])) {
                    $creation = $creation->withShortCodeLength((int) $data['short_code_length']);
                }
                if (! empty($data['tags'])) {
                    $creation = $creation->withTags(...$data['tags']);
                }
                if (! empty($data['valid_since'])) {
                    $creation = $creation->validSince(new \DateTimeImmutable($data['valid_since']));
                }
                if (! empty($data['valid_until'])) {
                    $creation = $creation->validUntil(new \DateTimeImmutable($data['valid_until']));
                }
                if (! empty($data['max_visits'])) {
                    $creation = $creation->withMaxVisits((int) $data['max_visits']);
                }
                if (! empty($data['crawlable'])) {
                    $creation = $creation->crawlable();
                }
                if (empty($data['forward_query'])) {
                    $creation = $creation->withoutQueryForwardingOnRedirect();
                }
                if (! empty($data['find_if_exists'])) {
                    $creation = $creation->returnExistingMatchingShortUrl();
                }

                try {
                    $service->createShortUrl($creation);
                    Notification::make()
                        ->title(__('filament-shlink::filament-shlink.short_url_created'))
                        ->success()
                        ->send();
                    $this->resetTable();
                } catch (\Throwable $e) {
                    Notification::make()
                        ->title(__('filament-shlink::filament-shlink.error_creating'))
                        ->body($e->getMessage())
                        ->danger()
                        ->send();
                }
            });
    }

    protected function getEditModalAction(): EditAction
    {
        return EditAction::make()
            ->mutateRecordDataUsing(function (ShortUrl $record): array {
                $identifier = ShortUrlIdentifier::fromShortCode($record->shortCode);
                $service = app(FilamentShlink::class);
                $sdkUrl = $service->getShortUrl($identifier);

                return [
                    'long_url' => $sdkUrl->longUrl,
                    'title' => $sdkUrl->title,
                    'short_code' => $sdkUrl->shortCode,
                    'domain' => $sdkUrl->domain,
                    'tags' => $sdkUrl->tags,
                    'valid_since' => $sdkUrl->meta->validSince?->format('Y-m-d H:i:s'),
                    'valid_until' => $sdkUrl->meta->validUntil?->format('Y-m-d H:i:s'),
                    'max_visits' => $sdkUrl->meta->maxVisits,
                    'crawlable' => $sdkUrl->crawlable,
                    'forward_query' => $sdkUrl->forwardQuery,
                ];
            })
            ->form([
                TextInput::make('long_url')
                    ->label(__('filament-shlink::filament-shlink.long_url'))
                    ->url()
                    ->columnSpanFull(),
                TextInput::make('title')
                    ->label(__('filament-shlink::filament-shlink.title')),
                TextInput::make('short_code')
                    ->label(__('filament-shlink::filament-shlink.short_code'))
                    ->disabled(),
                TextInput::make('domain')
                    ->label(__('filament-shlink::filament-shlink.domain'))
                    ->disabled(),
                TagsInput::make('tags')
                    ->label(__('filament-shlink::filament-shlink.tags')),
                DateTimePicker::make('valid_since')
                    ->label(__('filament-shlink::filament-shlink.valid_since')),
                DateTimePicker::make('valid_until')
                    ->label(__('filament-shlink::filament-shlink.valid_until')),
                TextInput::make('max_visits')
                    ->label(__('filament-shlink::filament-shlink.max_visits'))
                    ->numeric(),
                Toggle::make('crawlable')
                    ->label(__('filament-shlink::filament-shlink.crawlable')),
                Toggle::make('forward_query')
                    ->label(__('filament-shlink::filament-shlink.forward_query')),
            ])
            ->action(function (array $data, ShortUrl $record) {
                $service = app(FilamentShlink::class);
                $identifier = ShortUrlIdentifier::fromShortCode($record->shortCode);
                $edition = ShortUrlEdition::create();

                if (! empty($data['long_url'])) {
                    $edition = $edition->withLongUrl($data['long_url']);
                }
                if (! empty($data['title'])) {
                    $edition = $edition->withTitle($data['title']);
                }
                if (! empty($data['tags'])) {
                    $edition = $edition->withTags(...$data['tags']);
                } else {
                    $edition = $edition->withoutTags();
                }
                if (! empty($data['valid_since'])) {
                    $edition = $edition->validSince(new \DateTimeImmutable($data['valid_since']));
                }
                if (! empty($data['valid_until'])) {
                    $edition = $edition->validUntil(new \DateTimeImmutable($data['valid_until']));
                }
                if (! empty($data['max_visits'])) {
                    $edition = $edition->withMaxVisits((int) $data['max_visits']);
                }
                if (! empty($data['crawlable'])) {
                    $edition = $edition->crawlable();
                }
                if (! empty($data['forward_query'])) {
                    $edition = $edition->withQueryForwardingOnRedirect();
                }

                try {
                    $service->editShortUrl($identifier, $edition);
                    Notification::make()
                        ->title(__('filament-shlink::filament-shlink.short_url_updated'))
                        ->success()
                        ->send();
                    $this->resetTable();
                } catch (\Throwable $e) {
                    Notification::make()
                        ->title(__('filament-shlink::filament-shlink.error_updating'))
                        ->body($e->getMessage())
                        ->danger()
                        ->send();
                }
            });
    }
}
