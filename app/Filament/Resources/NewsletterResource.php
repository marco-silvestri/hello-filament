<?php

namespace App\Filament\Resources;

use AmidEsfahani\FilamentTinyEditor\TinyEditor;
use App\Enums\Cms\InternalNewsletterStatusEnum;
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
use App\Enums\Cms\PostStatusEnum;
use App\Forms\Components\RecentPost;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Builder\Block;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\NewsletterResource\Pages;
use Filament\Forms\Components\Builder as BuilderForm;
use App\Filament\Resources\NewsletterResource\RelationManagers;
use App\Models\Cms\NewsletterInternal;
use App\Models\Media;
use Closure;
use Filament\Forms\Components\Hidden;
use Filament\Tables\Actions\Action;
use Illuminate\Contracts\View\View;

class NewsletterResource extends Resource
{
    protected static ?string $model = Newsletter::class;

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';

    protected static ?string $navigationGroup = 'Contents';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('number')
                    ->default(function () {
                        return Newsletter::max('number') + 1;
                    }),
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
                    ->required()
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
                        Block::make('paragraph')
                            ->icon('heroicon-m-pencil')
                            ->label(__('common.lbl-paragraph'))
                            ->schema(
                                [
                                    TinyEditor::make('content')
                                        ->label('')
                                        ->imageList(Media::select('title', 'path')->get()->map(fn ($item, $key) => ['title' => $item->title, 'value' => $item->fullPath])->toArray())

                                ]
                            ),
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
                                            ->select(['feature_media_id', 'title AS label', 'id AS value', 'excerpt'])
                                            ->where('created_at', '>', Carbon::now()
                                                ->subMonths(2)
                                                ->format('Y-m-d H:i:s'))
                                            ->where('status', PostStatusEnum::PUBLISH)
                                            ->orderByDesc('published_at')
                                            ->get();
                                        $posts = $posts->map(function ($post, $value) {
                                            $label = view('filament.forms.components.recent-post')
                                                ->with('title', $post->label)
                                                ->with('excerpt', $post->excerpt)
                                                ->with('src', $post->featuredImage->url ?? '')
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
                                        if ($post->featuredImage) {
                                            $set('featureImage', ['id' => $post->featuredImage]);
                                        }
                                    })->native(false),
                                Group::make([
                                    CuratorPicker::make('featureImage')
                                        ->label(__('common.lbl-image')),
                                    Select::make('alignment')
                                        ->label(__('common.lbl-alignment'))
                                        ->options([
                                            'center' => __('common.lbl-align-center'),
                                            'left' => __('common.lbl-align-left'),
                                            'right' => __('common.lbl-align-right'),
                                        ])
                                        ->default('center')
                                        ->native(false),
                                ])->columns(2),
                                TextInput::make('title')
                                    ->label(__('common.lbl-title')),
                                Textarea::make('excerpt')
                                    ->label(__('common.lbl-excerpt')),
                            ]),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('newsletter.fld-name')),
                TextColumn::make('subject')
                    ->label(__('newsletter.fld-subject')),
                TextColumn::make('pre_header')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label(__('newsletter.fld-preheader')),
                TextColumn::make('send_date')
                    ->dateTime('d-m-Y H:i')
                    ->label(__('newsletter.fld-send-date')),
                TextColumn::make('number')
                    ->numeric()
                    ->label(__('common.fld-number')),
                TextColumn::make('status')
                    ->badge()
                    ->label(__('common.fld-status')),
                TextColumn::make('created_at')
                    ->dateTime('d-m-Y H:i')
                    ->label(__('common.fld-created-at')),
                TextColumn::make('updated_at')
                    ->dateTime('d-m-Y H:i')
                    ->label(__('common.fld-updated-at')),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Action::make('handle_workflow')
                    ->requiresConfirmation()
                    ->action(function(Newsletter $record){
                        if($record->status === InternalNewsletterStatusEnum::DRAFT)
                        {
                            $record->update(['status' => InternalNewsletterStatusEnum::SENT]);
                        } elseif($record->status === InternalNewsletterStatusEnum::SENT)
                        {
                            $record->update(['status' => InternalNewsletterStatusEnum::DRAFT]);
                        }
                    })->visible(
                        fn (Newsletter $record): bool =>
                            $record->status === InternalNewsletterStatusEnum::DRAFT
                            || $record->status === InternalNewsletterStatusEnum::SENT,
                    )->label(function(Newsletter $record){
                        if($record->status === InternalNewsletterStatusEnum::DRAFT)
                        {
                            return __('newsletter.fld-approve');
                        }

                        if($record->status === InternalNewsletterStatusEnum::SENT)
                        {
                            return __('newsletter.fld-send-to-draft');
                        }
                    })
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
