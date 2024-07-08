<?php

namespace App\Filament\Resources;

use App\Models\Tag;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\BulkActionGroup;
use App\Filament\Resources\TagResource\Pages;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Set;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Illuminate\Support\Str;

class TagResource extends Resource
{
    protected static ?string $model = Tag::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationGroup = 'Contents';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Textarea::make('name')
                    ->label(__('common.lbl-slug'))
                    ->required()
                    ->maxLength(65535)
                    ->live(debounce: 500)
                    ->afterStateUpdated(function (?string $state, ?string $old, Set $set) {
                        $set('slug.name', Str::of($state)->slug());
                    })
                    ->columnSpanFull(),
                Section::make()
                    ->relationship('slug')
                    ->schema([
                        TextInput::make('name')
                            ->label(__('common.lbl-slug'))
                            ->label('slug')
                            ->required()
                            ->readonly()
                            ->unique(table: 'slugs', column: 'name', ignoreRecord: true),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->numeric()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('name')
                    ->label(__('common.lbl-slug'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('posts_count')
                    ->label(__('common.lbl-post-count'))
                    ->counts('posts')
                    ->url(fn (Tag $tag): string => PostResource::getUrl('index', ['tableFilters' => ['tags' => ['values' => [$tag->id]]]]))
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('common.fld-created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
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
            'index' => Pages\ListTags::route('/'),
            'create' => Pages\CreateTag::route('/create'),
            'view' => Pages\ViewTag::route('/{record}'),
            'edit' => Pages\EditTag::route('/{record}/edit'),
        ];
    }

    public static function getModelLabel(): string
    {
        return __('common.ent-tag');
    }

    public static function getPluralModelLabel(): string
    {
        return __('common.ent-tags');
    }
}
