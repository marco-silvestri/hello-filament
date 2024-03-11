<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Category;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use App\Filament\Resources\CategoryResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\CategoryResource\RelationManagers;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Set;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Filters\Filter;
use Illuminate\Support\Str;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?string $navigationGroup = 'Contents';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label(__('common.fld-name'))
                    ->required()
                    ->live(debounce: 500)
                    ->afterStateUpdated(function (?string $state, ?string $old, Set $set) {
                        $set('slug.name', Str::of($state)->slug());
                    })
                    ->maxLength(65535)
                    ->columnSpanFull(),
                Section::make()
                    ->relationship('slug')
                    ->schema([
                        TextInput::make('name')
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
                    ->label(__('common.fld-name'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('posts_count')
                    ->label(__('common.lbl-post-count'))
                    ->counts('posts')
                    ->sortable()
                    ->url(fn (Category $category): string => PostResource::getUrl('index', ['tableFilters' => ['categories' => ['values' => [$category->id]]]]))
                    ->openUrlInNewTab(),
                TextColumn::make('created_at')
                    ->label(__('fld-created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
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
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'view' => Pages\ViewCategory::route('/{record}'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }

    public static function getModelLabel(): string
    {
        return __('common.ent-category');
    }

    public static function getPluralModelLabel(): string
    {
        return __('common.ent-categories');
    }
}
