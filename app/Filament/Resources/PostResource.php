<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Post;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use App\Enums\Cms\PostStatusEnum;
use BladeUI\Icons\Components\Icon;
use Filament\Forms\Components\Tabs;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\Filter;
use Illuminate\Contracts\View\View;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Section;
use FilamentTiptapEditor\TiptapEditor;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Tables\Actions\ActionGroup;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Builder\Block;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Forms\Components\DateTimePicker;
use Filament\Tables\Actions\DeleteBulkAction;
use App\Filament\Resources\PostResource\Pages;
use Awcodes\Curator\Components\Forms\Uploader;
use AmidEsfahani\FilamentTinyEditor\TinyEditor;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use App\Filament\Resources\PostResource\Pages\ViewPost;
use Illuminate\Contracts\Database\Eloquent\Builder as EloquentBuilder;

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
                                        TinyEditor::make('content')
                                            ->label('')
                                            ->profile('minimal')
                                            ->columnSpanFull()
                                            // ->disableBubbleMenus()
                                            // ->disableFloatingMenus(),
                                    ]),
                                Block::make('image')
                                    ->icon('heroicon-o-photo')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('width')
                                            ->columnSpan(1)
                                            ->numeric(),
                                        TextInput::make('height')
                                            ->columnSpan(1)
                                            ->numeric(),
                                        TextInput::make('alt')
                                            ->columnSpanFull(),
                                        TextInput::make('caption')
                                            ->columnSpanFull(),
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
                                Block::make('audio')
                                    ->schema([
                                        Select::make('audio')
                                            ->relationship(name: 'audio', titleAttribute: 'title')
                                            ->native(false)
                                            ->searchable()
                                            ->preload()
                                            ->createOptionForm(fn (Form $form) => AudioResource::formForModal($form)),
                                        TextInput::make('caption')
                                    ]),
                                Block::make('review')
                                    ->maxItems(1)
                                    ->columns(2)
                                    ->columnSpan(2)
                                    ->schema([
                                        Repeater::make('parameters')
                                            ->label(__('posts.lbl-parameter'))
                                            ->maxItems(10)
                                            ->columnSpanFull()
                                            ->columns(2)
                                            ->afterStateUpdated(function ($state, Set $set) {
                                                $tot = 0;
                                                $count = 0;
                                                foreach ($state as $param) {
                                                    $count++;
                                                    $tot += $param['value'];
                                                };
                                                if ($count > 0) {
                                                    $set('total_score', number_format(($tot / $count), 2));
                                                }
                                            })
                                            ->schema([
                                                TextInput::make('key')
                                                    ->required()
                                                    ->columnSpan(1),
                                                TextInput::make('value')
                                                    ->required()
                                                    ->live()
                                                    ->numeric()
                                                    ->extraInputAttributes(['min' => 0, 'max' => 10])
                                                    ->minValue(0)
                                                    ->maxValue(10)
                                                    ->step(0.1)
                                                    ->columnSpan(1),
                                            ]),
                                        Textarea::make('summary')
                                            ->label(__('posts.fl-summary')),
                                        TextInput::make('total_score')
                                            ->columnSpan(1)
                                            ->type('decimal')
                                            ->step(0.01)
                                            ->live()
                                    ])
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
                        Select::make('status')
                            ->options(PostStatusEnum::class),
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
                    ->limit(10)
                    ->limitList(4)
                    ->badge()
                    ->tooltip(function (TextColumn $column){
                        $state = $column->getState();
                        return implode(", ", $state);
                    }),
                TextColumn::make('categories.name')
                    ->limit(10)
                    ->limitList(2)
                    ->badge()
                    ->tooltip(function (TextColumn $column){
                        $state = $column->getState();
                        return implode(", ", $state);
                    }),
                TextColumn::make('visits_count')
                    ->counts('visits'),
                IconColumn::make('has_importer_problem')
                    ->boolean()
                    ->falseColor('info')
                    ->trueColor('warning')
                    ->falseIcon('heroicon-o-check')
                    ->trueIcon('heroicon-o-x-mark')
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->sortable(),
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
                    ->preload(),
                Filter::make('has_importer_problem')
                    ->query(fn(EloquentBuilder $query):EloquentBuilder => $query->where('has_importer_problem', true)),
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
        return [];
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
}
