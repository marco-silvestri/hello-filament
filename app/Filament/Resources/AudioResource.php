<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AudioResource\Pages;
use App\Filament\Resources\AudioResource\RelationManagers;
use App\Models\Audio;
use Awcodes\Curator\Components\Forms\Uploader;
use Filament\Forms;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Storage;

class AudioResource extends Resource
{
    protected static ?string $model = Audio::class;

    protected static ?string $navigationIcon = 'heroicon-o-musical-note';

    protected static ?string $navigationGroup = 'Attachments';

    public static function form(Form $form): Form
    {
        return $form
            ->columns(3)
            ->schema([
                Group::make()->schema([
                    Section::make('File')
                        ->hiddenOn('edit')
                        ->schema([
                            Uploader::make('file')
                                ->label('')
                                ->disk('public')
                                ->directory('audio')
                                ->maxSize(5000)
                                ->acceptedFileTypes(['audio/mpeg'])
                                ->required()
                                ->storeFileNamesIn('originalFilename')
                        ]),
                    Tabs::make('audio')
                        ->hiddenOn('create')
                        ->tabs([
                            Tab::make('Preview')
                                ->label('common.fld-preview')
                                ->schema([
                                    ViewField::make('preview')
                                        ->view('cms.filament.audio-track-preview')
                                        ->hiddenLabel()
                                        ->dehydrated(false)
                                        ->afterStateHydrated(function ($component, $state, $record) {
                                            $component->state($record);
                                        }),
                                ]),
                            Tab::make('Carica nuovo')
                                ->schema([
                                    Uploader::make('file')
                                        ->label('')
                                        ->disk('public')
                                        ->directory('audio')
                                        ->maxSize(5000)
                                        ->acceptedFileTypes(['audio/mpeg'])
                                        ->storeFileNamesIn('originalFilename')
                                ]),
                        ])
                ])
                    ->columnSpan([
                        'md' => 'full',
                        'lg' => 2,
                    ]),
                Section::make('Metadati')
                    ->columnSpan(1)
                    ->schema([
                        TextInput::make('title')
                            ->label(__('common.lbl-title'))
                            ->maxLength(255)
                            ->required(),
                        TextInput::make('description')
                            ->label(__('common.fld-description'))
                            ->maxLength(255),
                    ]),
                Section::make('Dettagli')
                    ->columnSpan(2)
                    ->columns(3)
                    ->schema([
                        Placeholder::make('created_at')
                            ->columnSpan(1)
                            ->label(__('common.fld-created-at'))
                            ->content(function (Audio $audio) {
                                if ($audio->created_at != null) {
                                    return $audio->created_at;
                                }
                                return '-';
                            }),
                        Placeholder::make('type')
                            ->columnSpan(1)
                            ->label(__('common.lbl-file-type'))
                            ->content(function (Audio $audio) {
                                if ($audio->type != null) {
                                    return $audio->type;
                                }
                                return '-';
                            }),
                        Placeholder::make('disk')
                            ->columnSpan(1)
                            ->label(__('common.lbl-disk'))
                            ->content(function (Audio $audio) {
                                if ($audio->disk != null) {
                                    return $audio->disk;
                                }
                                return '-';
                            }),
                        Placeholder::make('directory')
                            ->columnSpan(1)
                            ->label(__('common.lbl-directory'))
                            ->content(function (Audio $audio) {
                                if ($audio->directory != null) {
                                    return $audio->directory;
                                }
                                return '-';
                            }),
                        Placeholder::make('visibility')
                            ->columnSpan(1)
                            ->label(__('common.lbl-visibility'))
                            ->content(function (Audio $audio) {
                                if ($audio->visibility != null) {
                                    return $audio->visibility;
                                }
                                return '-';
                            }),
                        Placeholder::make('path')
                            ->columnSpan(3)
                            ->label(__('common.lbl-path'))
                            ->content(function (Audio $audio) {
                                if ($audio->path != null) {
                                    return Storage::disk($audio->disk)->url($audio->path);
                                }
                                return '-';
                            }),
                    ]),
            ]);
    }

    public static function formForModal(Form $form): Form
    {
        return $form
            ->columns(3)
            ->schema([
                Group::make()->schema([
                    Section::make('File')
                        ->hiddenOn('edit')
                        ->schema([
                            Uploader::make('file')
                                ->label('')
                                ->disk('public')
                                ->directory('audio')
                                ->maxSize(12000)
                                ->acceptedFileTypes(['audio/mpeg'])
                                ->required()
                                ->storeFileNamesIn('originalFilename')
                                ->after(function (Set $set, $state) {
                                    #NON ENTRANDO NEL LYFECYCLE DELLA CLASSE BISOGNA SETTARE COSì LE VARIE VARIABILI
                                    $key = array_key_first($state);
                                    $set('ext', $state[$key]->getClientOriginalExtension());
                                    $set('title', $state[$key]->getClientOriginalName());
                                    $set('type', $state[$key]->getMimeType());
                                    $set('name', $key);
                                    $set('disk', 'public');
                                    $set('directory', 'audio');
                                    $set('path', 'audio/' . $key . $state[$key]->getClientOriginalExtension());
                                }),
                            Hidden::make('disk'),
                            Hidden::make('directory'),
                            Hidden::make('name'),
                            Hidden::make('path'),
                            Hidden::make('type'),
                            Hidden::make('ext')
                        ]),
                ])
                    ->columnSpan([
                        'md' => 'full',
                        'lg' => 2,
                    ]),
                Section::make('Metadati')
                    ->columnSpan(1)
                    ->schema([
                        TextInput::make('title')
                            ->label("common.lbl-title")
                            ->maxLength(255)
                            ->required(),
                        TextInput::make('description')
                            ->label("common.fld-description")
                            ->maxLength(255),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('disk')
                    ->label(__('common.lbl-disk'))
                    ->searchable(),
                TextColumn::make('directory')
                    ->label(__('common.lbl-directory'))
                    ->searchable(),
                TextColumn::make('visibility')
                    ->label(__('common.lbl-visibility'))
                    ->searchable(),
                TextColumn::make('name')
                    ->label(__('common.fld-name'))
                    ->searchable(),
                TextColumn::make('path')
                    ->label(__('common.lbl-path'))
                    ->searchable(),
                TextColumn::make('type')
                    ->label(__('common.lbl-file-type'))
                    ->searchable(),
                TextColumn::make('ext')
                    ->label(__('common.lbl-ext'))
                    ->searchable(),
                TextColumn::make('title')
                    ->label(__('common.lbl-title'))
                    ->searchable(),
                TextColumn::make('attributes')
                    ->label(__('common.lbl-attributes'))
                    ->searchable(),
                TextColumn::make('description')
                    ->label(__("common.fld-description"))
                    ->searchable(),
                TextColumn::make('deleted_at')
                    ->label("common.fld-deleted-at")
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label("common.fld-created-at")
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label("common.fld-updated-at")
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAudio::route('/'),
            'create' => Pages\CreateAudio::route('/create'),
            'edit' => Pages\EditAudio::route('/{record}/edit'),
        ];
    }

    public static function getModelLabel(): string
    {
        return __('common.ent-audio');
    }

    public static function getPluralModelLabel(): string
    {
        return __('common.ent-audio');
    }
}
