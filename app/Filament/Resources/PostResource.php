<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Post;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use FilamentTiptapEditor\TiptapEditor;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use App\Filament\Resources\PostResource\Pages;
use AmidEsfahani\FilamentTinyEditor\TinyEditor;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Illuminate\Contracts\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Contents';

    public static function form(Form $form): Form
    {
        return $form
            ->columns(3)
            ->schema([
                Section::make()
                    ->columnSpan(2)
                    ->schema([
                        Textarea::make('title')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->live(debounce: 500)
                            ->afterStateUpdated(function (?string $state, ?string $old, Set $set) {
                                $set('slug.name', Str::of($state)->slug());
                            })
                            ->maxLength(255),
                        Section::make()
                        ->relationship('slug')
                        ->schema([
                            TextInput::make('name')
                            ->label('slug')
                            ->required()
                            ->readonly()
                            ->unique(table: 'slugs', column: 'name', ignoreRecord: true),
                        ]),
                        Builder::make('json_content')
                            ->label('Content')
                            ->columnSpanFull()
                            ->addActionLabel('Add a new block')
                            ->reorderableWithButtons()
                            ->collapsible()
                            ->blocks([
                                Block::make('heading')
                                    ->icon('heroicon-m-pencil')
                                    ->schema([
                                        TextInput::make('content')
                                            ->label('Heading')
                                            ->required(),
                                        Select::make('level')
                                            ->options([
                                                'h1' => 'Heading 1',
                                                'h2' => 'Heading 2',
                                                'h3' => 'Heading 3',
                                                'h4' => 'Heading 4',
                                                'h5' => 'Heading 5',
                                                'h6' => 'Heading 6',
                                            ]),
                                        ColorPicker::make('color')
                                    ])->columns(2),
                                Block::make('paragraph')
                                    ->icon('heroicon-m-bars-3-bottom-left')
                                    ->schema([
                                        TiptapEditor::make('content')
                                            ->label('')
                                            ->profile('minimal')
                                            ->columnSpanFull()
                                            ->disableBubbleMenus()
                                            ->disableFloatingMenus(),
                                    ]),
                                Block::make('image')
                                    ->icon('heroicon-o-photo')
                                    ->schema([
                                        TextInput::make('width')
                                        ->numeric(),
                                        TextInput::make('height')
                                        ->numeric(),
                                        CuratorPicker::make('image')
                                    ]),
                                Block::make('related_posts')
                                    ->icon('heroicon-m-clipboard-document-list')
                                    ->maxItems(1)
                                    ->schema([
                                        Select::make('related_posts')
                                            ->options(Post::all()->pluck('title', 'id'))
                                            ->searchable()
                                            ->multiple()
                                    ]),
                                Block::make('video')
                                    ->schema([
                                        TextInput::make('url')
                                            ->label('Video Url')
                                            ->required(),
                                    ]),
                            ]),
                        Textarea::make('excerpt')
                            ->required()
                            ->maxLength(16777215)
                            ->columnSpanFull(),
                    ]),
                Section::make()
                    ->columnSpan(1)
                    ->schema([
                        Select::make('author_id')
                            ->relationship('author', 'name', fn (EloquentBuilder $query) => $query->role('author'),)
                            ->native(false)
                            ->required(),
                        Select::make('categories')
                            ->relationship(name: 'categories', titleAttribute: 'name')
                            ->multiple()
                            ->searchable(),
                        Select::make('tags')
                            ->relationship(name: 'tags', titleAttribute: 'name')
                            ->multiple()
                            ->searchable(),
                        CuratorPicker::make('feature_media_id')
                            ->label('Featured image'),
                        Textarea::make('status')
                            ->required()
                            ->maxLength(65535)
                            ->columnSpanFull(),
                        DateTimePicker::make('published_at')
                            ->label('Published at')
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('author.name')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('title')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('slug.name'),
                TextColumn::make('tags.name')
                    ->badge(),
                TextColumn::make('categories.name')
                    ->badge(),
                TextColumn::make('visits_count')
                    ->counts('visits'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('tags')
                    ->relationship('tags', 'name')
                    ->multiple()
                    ->preload(),
                SelectFilter::make('categories')
                    ->relationship('categories', 'name')
                    ->multiple()
                    ->preload()
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                    Action::make('Post preview')
                        ->modalContent(fn (Post $record): View => view(
                            'filament.pages.actions.post-preview',
                            ['record' => $record],
                        ))
                ])
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
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
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'view' => Pages\ViewPost::route('/{record}'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }

    protected function beforeCreate(): void
    {
        dd('in');
    }
}
