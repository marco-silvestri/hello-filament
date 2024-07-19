<?php

namespace App\Filament\Resources;

use App\Models\Tag;
use Filament\Forms;
use Filament\Tables;
use App\Models\Category;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\HomePageSetting;
use Filament\Resources\Resource;
use App\Enums\Cms\DisplayableAsEnum;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\MorphToSelect;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\HomePageSettingResource\Pages;
use App\Filament\Resources\HomePageSettingResource\RelationManagers;

class HomePageSettingResource extends Resource
{
    protected static ?string $model = HomePageSetting::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                MorphToSelect::make('groupable')
                ->label(__('homepage-settings.lbl-groupable'))
                    ->types([
                        MorphToSelect\Type::make(Category::class)
                            ->titleAttribute('name')
                            ->getOptionLabelFromRecordUsing(fn (Category $record): string
                                => $record->name . "(" . $record->posts->count() .")"),
                        MorphToSelect\Type::make(Tag::class)
                            ->titleAttribute('name')
                            ->getOptionLabelFromRecordUsing(fn (Tag $record): string
                                => $record->name . "(" . $record->posts->count() .")"),
                    ])->native(false)->searchable(),
                Select::make('displayable_as')
                    ->label(__('homepage-settings.lbl-displayable-as'))
                    ->options(array_flip(DisplayableAsEnum::toArray()))
                    ->default(DisplayableAsEnum::STRIP->value)->native(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->numeric()
                    ->hidden(),
                TextColumn::make('groupable.name')
                    ->badge(),
                ToggleColumn::make('visibility'),
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
            ])
            ->defaultGroup('displayable_as')
            ->reorderable('order_by');
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
            'index' => Pages\ListHomePageSettings::route('/'),
            'create' => Pages\CreateHomePageSetting::route('/create'),
            'edit' => Pages\EditHomePageSetting::route('/{record}/edit'),
        ];
    }
}
