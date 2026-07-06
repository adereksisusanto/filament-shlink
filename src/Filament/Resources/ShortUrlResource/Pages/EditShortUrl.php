<?php

namespace Adereksisusanto\FilamentShlink\Filament\Resources\ShortUrlResource\Pages;

use Adereksisusanto\FilamentShlink\Filament\Resources\ShortUrlResource;
use Adereksisusanto\FilamentShlink\FilamentShlink;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Schema;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrlEdition;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrlIdentifier;

class EditShortUrl extends Page
{
    protected static string $resource = ShortUrlResource::class;

    protected string $view = 'filament-shlink::short-url-form';

    public ?array $data = [];

    public string $shortCode;

    public function mount(string $record): void
    {
        $this->shortCode = $record;
        $service = app(FilamentShlink::class);

        try {
            $identifier = ShortUrlIdentifier::fromShortCode($record);
            $sdkUrl = $service->getShortUrl($identifier);

            $this->form->fill([
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
            ]);
        } catch (\Throwable $e) {
            Notification::make()
                ->title(__('filament-shlink::filament-shlink.error_loading'))
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->statePath('data')
            ->components([
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
            ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $service = app(FilamentShlink::class);
        $identifier = ShortUrlIdentifier::fromShortCode($this->shortCode);

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
            $this->redirect(ShortUrlResource::getUrl('index'));
        } catch (\Throwable $e) {
            Notification::make()
                ->title(__('filament-shlink::filament-shlink.error_updating'))
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label(__('filament-shlink::filament-shlink.save'))
                ->submit('save'),
        ];
    }
}
