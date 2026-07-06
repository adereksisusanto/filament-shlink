<?php

namespace Adereksisusanto\FilamentShlink\Filament\Pages;

use Adereksisusanto\FilamentShlink\FilamentShlink;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ShlinkSettings extends Page
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-cog-6-tooth';

    public static function getNavigationGroup(): ?string
    {
        return __('filament-shlink::filament-shlink.navigation_group');
    }

    protected string $view = 'filament-shlink::settings';

    protected static ?string $slug = 'shlink-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'server_url' => config('filament-shlink.server_url'),
            'api_key' => config('filament-shlink.api_key'),
        ]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->statePath('data')
            ->components([
                Section::make(__('filament-shlink::filament-shlink.shlink_connection'))
                    ->description(__('filament-shlink::filament-shlink.shlink_connection_description'))
                    ->schema([
                        TextInput::make('server_url')
                            ->label(__('filament-shlink::filament-shlink.server_url'))
                            ->required()
                            ->url()
                            ->placeholder('https://shlink.example.com'),
                        TextInput::make('api_key')
                            ->label(__('filament-shlink::filament-shlink.api_key'))
                            ->required()
                            ->password()
                            ->revealable(),
                    ]),
            ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $service = app(FilamentShlink::class);
        $service->setConfig($data['server_url'], $data['api_key']);

        try {
            $result = iterator_count($service->listDomains());
        } catch (\Throwable $e) {
            Notification::make()
                ->title(__('filament-shlink::filament-shlink.connection_failed'))
                ->body($e->getMessage())
                ->danger()
                ->send();

            return;
        }

        $envPath = app()->environmentFilePath();
        $envContent = file_get_contents($envPath);

        $replacements = [
            'SHLINK_SERVER_URL' => $data['server_url'],
            'SHLINK_API_KEY' => $data['api_key'],
        ];

        foreach ($replacements as $key => $value) {
            $pattern = sprintf('/^%s=.*/m', preg_quote($key, '/'));
            $replacement = $key . '=' . $value;
            if (preg_match($pattern, $envContent)) {
                $envContent = preg_replace($pattern, $replacement, $envContent);
            } else {
                $envContent .= PHP_EOL . $replacement;
            }
        }

        file_put_contents($envPath, $envContent);

        Notification::make()
            ->title(__('filament-shlink::filament-shlink.settings_saved'))
            ->success()
            ->send();
    }

    public static function getNavigationLabel(): string
    {
        return __('filament-shlink::filament-shlink.settings');
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
