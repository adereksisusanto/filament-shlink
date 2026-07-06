<?php

namespace Adereksisusanto\FilamentShlink\Filament\Resources\TagResource\Pages;

use Adereksisusanto\FilamentShlink\Filament\Resources\TagResource;
use Adereksisusanto\FilamentShlink\FilamentShlink;
use Adereksisusanto\FilamentShlink\Models\Tag;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Shlinkio\Shlink\SDK\Tags\Model\TagRenaming;

class ListTags extends ListRecords
{
    protected static string $resource = TagResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('refresh')
                ->label(__('filament-shlink::filament-shlink.refresh'))
                ->icon('heroicon-o-arrow-path')
                ->action(fn () => $this->resetTable()),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->records(fn (): \Illuminate\Support\Collection => $this->getTableRecords())
            ->columns([
                TextColumn::make('name')
                    ->label(__('filament-shlink::filament-shlink.tag_name'))
                    ->searchable(),
                TextColumn::make('shortUrlsCount')
                    ->label(__('filament-shlink::filament-shlink.short_urls_count')),
                TextColumn::make('totalVisits')
                    ->label(__('filament-shlink::filament-shlink.visits')),
            ])
            ->actions([
                EditAction::make()
                    ->form([
                        TextInput::make('new_name')
                            ->label(__('filament-shlink::filament-shlink.new_tag_name'))
                            ->required(),
                    ])
                    ->action(function (Tag $record, array $data) {
                        try {
                            app(FilamentShlink::class)->renameTag(
                                TagRenaming::fromOldNameAndNewName($record->name, $data['new_name']),
                            );
                            Notification::make()->title(__('filament-shlink::filament-shlink.tag_renamed'))->success()->send();
                        } catch (\Throwable $e) {
                            Notification::make()->title(__('filament-shlink::filament-shlink.error_renaming_tag'))->body($e->getMessage())->danger()->send();
                        }
                        $this->resetTable();
                    }),
                DeleteAction::make()
                    ->action(function (Tag $record) {
                        try {
                            app(FilamentShlink::class)->deleteTags($record->name);
                            Notification::make()->title(__('filament-shlink::filament-shlink.tag_deleted'))->success()->send();
                        } catch (\Throwable $e) {
                            Notification::make()->title(__('filament-shlink::filament-shlink.error_deleting_tag'))->body($e->getMessage())->danger()->send();
                        }
                        $this->resetTable();
                    }),
            ]);
    }

    public function getTableRecords(): \Illuminate\Support\Collection
    {
        try {
            $service = app(FilamentShlink::class);
            if (! $service->isConfigured()) {
                return collect();
            }

            $tagsWithStats = $service->listTagsWithStats();
            $records = [];
            foreach ($tagsWithStats as $tagStats) {
                $model = Tag::fromShlinkTagWithStats($tagStats);
                $records[$model->getKey()] = $model;
            }

            return collect($records);
        } catch (\Throwable $e) {
            Notification::make()->title($e->getMessage())->danger()->send();

            return collect();
        }
    }
}
