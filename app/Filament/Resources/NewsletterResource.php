<?php

namespace App\Filament\Resources;

use App\Enums\Cms\PostStatusEnum;
use Carbon\Carbon;
use Filament\Forms;
use App\Models\Post;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use App\Models\Newsletter;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Forms\Components\RecentPost;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Builder\Block;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\NewsletterResource\Pages;
use Filament\Forms\Components\Builder as BuilderForm;
use App\Filament\Resources\NewsletterResource\RelationManagers;
use Closure;
use Filament\Forms\Components\Textarea;

class NewsletterResource extends Resource
{
    protected static ?string $model = Newsletter::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('number')
                    ->default(function () {
                        return Newsletter::max('number') + 1;
                    })->hidden(),
                TextInput::make('name')
                    ->default(function (Get $get) {
                        $date = Carbon::now()->format('d/m/Y');
                        return "Newsletter n. {$get('number')} del {$date}";
                    }),
                TextInput::make('subject')
                    ->default(function () {
                        return 'Audiofader news';
                    }),
                BuilderForm::make('json_content')
                    ->label(__('common.lbl-content'))
                    ->columnSpanFull()
                    ->addActionLabel(__('block-builder.lbl-add-block'))
                    ->reorderableWithButtons()
                    ->collapsible()
                    ->blocks([
                        Block::make('heading')
                            ->icon('heroicon-m-pencil')
                            ->schema([
                                TextInput::make('content')
                                    ->label(__('common.lbl-heading'))
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
                        Block::make('related_posts')
                            ->icon('heroicon-m-clipboard-document-list')
                            ->schema([
                                Select::make('posts')
                                    ->label(__('block-builder.lbl-related'))
                                    ->allowHtml()
                                    ->searchable()
                                    ->preload()
                                    ->options(function () {
                                        $posts = Post::query()
                                            ->with('featuredImage')
                                            ->select(['feature_media_id','title AS label', 'id AS value', 'excerpt'])
                                            ->where('created_at', '>', Carbon::now()
                                                ->subMonth()
                                                ->format('Y-m-d H:i:s'))
                                            ->where('status', PostStatusEnum::PUBLISH)
                                            ->orderByDesc('published_at')
                                            ->get();

                                        $posts = $posts->map(function ($post, $value) {

                                            $label = view('filament.forms.components.recent-post')
                                                ->with('title', $post->label)
                                                ->with('excerpt', $post->excerpt)
                                                ->with('src', $post->featuredImage->url)
                                                ->render();
                                            $post->label = $label;
                                            return $post;
                                        });

                                        return $posts->pluck('label', 'value')->toArray();
                                    })
                                    ->live()
                                    ->afterStateUpdated(function (Set $set, $state) {
                                        $post = Post::find($state);
                                        $set('title', $post?->title);
                                        $set('excerpt', $post?->excerpt);
                                    })->native(false),
                                Select::make('alignment')
                                    ->label(__('common.lbl-alignment'))
                                    ->options([
                                        'center' => __('common.lbl-align-center'),
                                        'left' => __('common.lbl-align-left'),
                                        'right' => __('common.lbl-align-right'),
                                    ])
                                    ->default('center')
                                    ->native(false),
                                TextInput::make('title')
                                    ->label(__('common.lbl-title')),
                                Textarea::make('excerpt')
                                    ->label(__('common.lbl-excerpt')),
                                // TextInput::make('image'),
                                //RecentPost::make('recent_post'),
                                // Select::make('related_posts')
                                //     ->options(Post::query()
                                //         ->where('created_at', '>', Carbon::now()
                                //             ->subMonth()
                                //             ->format('Y-m-d H:i:s'))
                                //         ->pluck('title', 'id'))
                                //     ->searchable(),
                            ]),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('subject'),
                TextColumn::make('pre_header')
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('send_date'),
                TextColumn::make('number')
                    ->numeric(),
                TextColumn::make('status')
                    ->badge(),
                TextColumn::make('created_at'),
                TextColumn::make('updated_at',)
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
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
            'index' => Pages\ListNewsletters::route('/'),
            'create' => Pages\CreateNewsletter::route('/create'),
            'view' => Pages\ViewNewsletter::route('/{record}'),
            'edit' => Pages\EditNewsletter::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
