<?php

namespace App\Filament\Resources;

use App\Models\Post;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use App\Enums\Cms\PostAccessEnum;
use App\Enums\Cms\PostStatusEnum;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\Filter;
use Illuminate\Contracts\View\View;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Section;
use FilamentTiptapEditor\TiptapEditor;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ActionGroup;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Builder\Block;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Forms\Components\DateTimePicker;
use Filament\Tables\Actions\DeleteBulkAction;
use App\Filament\Resources\PostResource\Pages;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Illuminate\Contracts\Database\Eloquent\Builder as EloquentBuilder;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-pencil-square';

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
                            ->label(__('common.lbl-title'))
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
                            ->label(__('block-builder.content'))
                            ->columnSpanFull()
                            ->addActionLabel(__('block-builder.add-block'))
                            ->reorderableWithButtons()
                            ->collapsible()
                            ->blocks([
                                // Block::make('heading')
                                //     ->label(__('block-builder.heading'))
                                //     ->icon('heroicon-m-pencil')
                                //     ->schema([
                                //         TextInput::make('content')
                                //             ->label(__('block-builder.content'))
                                //             ->required(),
                                //         Select::make('level')
                                //             ->label(__('block-builder.level'))
                                //             ->options([
                                //                 'h1' => 'Heading 1',
                                //                 'h2' => 'Heading 2',
                                //                 'h3' => 'Heading 3',
                                //                 'h4' => 'Heading 4',
                                //                 'h5' => 'Heading 5',
                                //                 'h6' => 'Heading 6',
                                //             ]),
                                //         ColorPicker::make('color')
                                //             ->label(__('block-builder.color'))
                                //     ])->columns(2),
                                Block::make('paragraph')
                                    ->label(__('block-builder.paragraph'))
                                    ->icon('heroicon-m-bars-3-bottom-left')
                                    ->schema([
                                        TiptapEditor::make('content')
                                            ->label(__('block-builder.content'))
                                            ->profile('simple')
                                    ]),
                                Block::make('image')
                                    ->label(__('block-builder.image'))
                                    ->icon('heroicon-o-photo')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('width')
                                            ->label(__('block-builder.width'))
                                            ->columnSpan(1)
                                            ->numeric(),
                                        TextInput::make('height')
                                            ->label(__('block-builder.height'))
                                            ->columnSpan(1)
                                            ->numeric(),
                                        TextInput::make('alt')
                                            ->columnSpanFull(),
                                        TextInput::make('caption')
                                            ->columnSpanFull(),
                                        CuratorPicker::make('image')
                                            ->label(__('block-builder.image'))
                                    ]),
                                Block::make('video')
                                    ->icon('heroicon-o-video-camera')
                                    ->schema([
                                        TextInput::make('url')
                                            ->label('Video Url')
                                            ->required(),
                                    ]),
                                Block::make('audio')
                                    ->icon('heroicon-o-musical-note')
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
                                    ->icon('heroicon-o-clipboard-document-list')
                                    ->label(__('block-builder.review'))
                                    ->maxItems(1)
                                    ->columns(2)
                                    ->columnSpan(2)
                                    ->schema([
                                        Repeater::make('parameters')
                                            ->label(__('posts.lbl-parameters'))
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
                                                    ->label(__('block-builder.key'))
                                                    ->required()
                                                    ->columnSpan(1),
                                                TextInput::make('value')
                                                    ->label(__('block-builder.value'))
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
                                            ->label(__('block-builder.score'))
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
                            ->label(__('common.fld-author'))
                            ->relationship('author', 'name', fn (EloquentBuilder $query) => $query->role('author'),)
                            ->native(false)
                            ->required(),
                        Select::make('categories')
                            ->label(__('common.ent-categories'))
                            ->relationship(name: 'categories', titleAttribute: 'name')
                            ->multiple()
                            ->searchable(),
                        Select::make('tags')
                            ->label(__('common.ent-tags'))
                            ->relationship(name: 'tags', titleAttribute: 'name')
                            ->multiple()
                            ->searchable(),
                        Select::make('related_post')
                            ->label(__('posts.lbl-related-posts'))
                            ->relationship(name: 'relatedPosts', titleAttribute: 'title')
                            ->multiple()
                            ->searchable(),
                        CuratorPicker::make('feature_media_id')
                            ->label(__('posts.lbl-featured-image')),
                        Select::make('status')
                            ->label(__('posts.lbl-status'))
                            ->required()
                            ->options(PostStatusEnum::class),
                        DateTimePicker::make('published_at')
                            ->label(__('posts.lbl-published-at'))
                            ->native(false)
                            ->displayFormat('d/m/Y H:i:s')
                            ->required(),
                        Group::make()
                            ->label('Settings')
                            ->relationship('settings')
                            ->schema([
                                Select::make('accessible_for')
                                    ->label(__('posts.lbl-accessible-for'))
                                    ->options(PostAccessEnum::class)
                                    ->default(PostAccessEnum::FREE)
                                    ->required(),
                                DatePicker::make('highlighted')
                                    ->label(__('posts.lbl-highlighted'))
                                    ->native(false)
                                    ->displayFormat('d/m/Y'),

                            ]),
                        Repeater::make('plannings')
                            ->relationship()
                            ->label(__('posts.lbl-plannings'))
                            ->defaultItems(0)
                            ->addActionLabel(__('posts.lbl-new-planning'))
                            ->schema([
                                DatePicker::make('start_at')
                                    ->label(__('posts.lbl-published-from'))
                                    ->native(false)
                                    ->displayFormat('d/m/Y')
                                    ->required(),
                                DatePicker::make('end_at')
                                    ->label(__('posts.lbl-published-to'))
                                    ->native(false)
                                    ->displayFormat('d/m/Y')
                                    ->required(),
                            ])



                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('author.name')
                    ->label(__('common.fld-author'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('title')
                    ->label(__('common.lbl-title'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('slug.name')
                    ->label(__('common.lbl-slug')),
                TextColumn::make('tags.name')
                    ->limit(10)
                    ->limitList(4)
                    ->badge()
                    ->tooltip(function (TextColumn $column) {
                        $state = $column->getState();
                        if ($state) {
                            return implode(", ", $state);
                        }
                    }),
                TextColumn::make('categories.name')
                    ->label(__('common.ent-categories'))
                    ->limit(10)
                    ->limitList(2)
                    ->badge()
                    ->tooltip(function (TextColumn $column) {
                        $state = $column->getState();
                        if ($state) {
                            return implode(", ", $state);
                        }
                    }),
                TextColumn::make('visits_count')
                    ->label(__('posts.lbl-visits-count'))
                    ->counts('visits'),
                IconColumn::make('has_importer_problem')
                    ->label(__('posts.lbl-importer-problem'))
                    ->boolean()
                    ->falseColor('info')
                    ->trueColor('warning')
                    ->falseIcon('heroicon-o-check')
                    ->trueIcon('heroicon-o-x-mark')
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('common.fld-created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('common.fld-updated-at'))
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
                    ->query(fn (EloquentBuilder $query): EloquentBuilder => $query->where('has_importer_problem', true)),
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
            ])->defaultSort('created_at', 'desc');
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

    public static function getModelLabel(): string
    {
        return __('common.ent-post');
    }

    public static function getPluralModelLabel(): string
    {
        return __('common.ent-posts');
    }
}
