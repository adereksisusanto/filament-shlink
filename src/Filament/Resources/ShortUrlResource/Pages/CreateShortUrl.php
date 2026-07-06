<?php

namespace Adereksisusanto\FilamentShlink\Filament\Resources\ShortUrlResource\Pages;

use Adereksisusanto\FilamentShlink\Filament\Resources\ShortUrlResource;
use Adereksisusanto\FilamentShlink\FilamentShlink;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Schema;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrlCreation;

class CreateShortUrl extends Page
{
    protected static string $resource = ShortUrlResource::class;

    protected string $view = 'filament-shlink::short-url-form';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->statePath('data')
            ->components([
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
            ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();
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
            $this->redirect(ShortUrlResource::getUrl('index'));
        } catch (\Throwable $e) {
            Notification::make()
                ->title(__('filament-shlink::filament-shlink.error_creating'))
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
