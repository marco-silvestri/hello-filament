<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Filament\Resources\PostResource;
use App\Models\Post;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Actions;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Form;
use Filament\Resources\Pages\ViewRecord;
use FilamentTiptapEditor\TiptapEditor;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

class ViewPost extends ViewRecord
{
    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }


    // public function form(Form $form): Form
    // {
    //     return $form
    //         ->columns(3)
    //         ->schema([
    //             Section::make()
    //                 ->columnSpan(2)
    //                 ->schema([
    //                     Textarea::make('title')
    //                         ->required()
    //                         ->unique(ignoreRecord: true)
    //                         ->live(debounce: 500)
    //                         ->afterStateUpdated(function (?string $state, ?string $old, Set $set) {
    //                             $set('slug.name', Str::of($state)->slug());
    //                         })
    //                         ->maxLength(255),
    //                     Section::make()
    //                         ->relationship('slug')
    //                         ->schema([
    //                             TextInput::make('name')
    //                                 ->label('slug')
    //                                 ->required()
    //                                 ->readonly()
    //                                 ->unique(table: 'slugs', column: 'name', ignoreRecord: true),
    //                         ]),
    //                     Builder::make('json_content')
    //                         ->label('Content')
    //                         ->columnSpanFull()
    //                         ->addActionLabel('Add a new block')
    //                         ->reorderableWithButtons()
    //                         ->collapsible()
    //                         ->blocks([
    //                             Block::make('heading')
    //                                 ->icon('heroicon-m-pencil')
    //                                 ->schema([
    //                                     TextInput::make('content')
    //                                         ->label('Heading')
    //                                         ->required(),
    //                                     Select::make('level')
    //                                         ->options([
    //                                             'h1' => 'Heading 1',
    //                                             'h2' => 'Heading 2',
    //                                             'h3' => 'Heading 3',
    //                                             'h4' => 'Heading 4',
    //                                             'h5' => 'Heading 5',
    //                                             'h6' => 'Heading 6',
    //                                         ]),
    //                                     ColorPicker::make('color')
    //                                 ])->columns(2),
    //                             Block::make('paragraph')
    //                                 ->icon('heroicon-m-bars-3-bottom-left')
    //                                 ->schema([
    //                                     TiptapEditor::make('content')
    //                                         ->label('')
    //                                         ->profile('minimal')
    //                                         ->columnSpanFull()
    //                                         ->disableBubbleMenus()
    //                                         ->disableFloatingMenus(),
    //                                 ]),
    //                             Block::make('image')
    //                                 ->icon('heroicon-o-photo')
    //                                 ->columns(2)
    //                                 ->schema([
    //                                     TextInput::make('width')
    //                                         ->columnSpan(1)
    //                                         ->numeric(),
    //                                     TextInput::make('height')
    //                                         ->columnSpan(1)
    //                                         ->numeric(),
    //                                     TextInput::make('alt')
    //                                         ->columnSpanFull(),
    //                                     TextInput::make('caption')
    //                                         ->columnSpanFull(),
    //                                     CuratorPicker::make('image')
    //                                 ]),
    //                             Block::make('related_posts')
    //                                 ->icon('heroicon-m-clipboard-document-list')
    //                                 ->maxItems(1)
    //                                 ->schema([
    //                                     Select::make('related_posts')
    //                                         ->options(Post::all()->pluck('title', 'id'))
    //                                         ->searchable()
    //                                         ->multiple()
    //                                 ]),
    //                             Block::make('video')
    //                                 ->schema([
    //                                     TextInput::make('url')
    //                                         ->label('Video Url')
    //                                         ->required(),
    //                                 ]),
    //                             Block::make('audio')
    //                                 ->schema([
    //                                     ViewField::make('audio')
    //                                         ->hiddenOn(['edit', 'create'])
    //                                         ->view('cms.filament.audio-track-preview')
    //                                         ->hiddenLabel()
    //                                         ->dehydrated(false)
    //                                         ->afterStateHydrated(function ($component, $state, $record) {
    //                                             dd($state);
    //                                         })
    //                                 ]),
    //                         ]),
    //                     Textarea::make('excerpt')
    //                         ->required()
    //                         ->maxLength(16777215)
    //                         ->columnSpanFull(),
    //                 ]),
    //             Section::make()
    //                 ->columnSpan(1)
    //                 ->schema([
    //                     Select::make('author_id')
    //                         ->relationship('author', 'name', fn (EloquentBuilder $query) => $query->role('author'),)
    //                         ->native(false)
    //                         ->required(),
    //                     Select::make('categories')
    //                         ->relationship(name: 'categories', titleAttribute: 'name')
    //                         ->multiple()
    //                         ->searchable(),
    //                     Select::make('tags')
    //                         ->relationship(name: 'tags', titleAttribute: 'name')
    //                         ->multiple()
    //                         ->searchable(),
    //                     CuratorPicker::make('feature_media_id')
    //                         ->label('Featured image'),
    //                     Textarea::make('status')
    //                         ->required()
    //                         ->maxLength(65535)
    //                         ->columnSpanFull(),
    //                     DateTimePicker::make('published_at')
    //                         ->label('Published at')
    //                         ->required(),
    //                 ]),
    //         ]);
    // }
}
