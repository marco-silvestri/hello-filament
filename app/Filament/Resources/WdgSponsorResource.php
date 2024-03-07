<?php

namespace App\Filament\Resources;

use App\Enums\Cms\WdgSponsorTypeEnum;
use App\Filament\Resources\WdgSponsorResource\Pages;
use App\Filament\Resources\WdgSponsorResource\RelationManagers;
use App\Models\WdgSponsor;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Split;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class WdgSponsorResource extends Resource
{
    protected static ?string $model = WdgSponsor::class;

    protected static ?string $navigationIcon = 'heroicon-o-code-bracket-square';

    protected static ?string $modelLabel = 'Sponsor';

    protected static ?string $navigationGroup = 'Widget';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                    Hidden::make('type')->default(WdgSponsorTypeEnum::BADGE->value),
                ])->columnSpan(1),
                Section::make('extra data')
                    ->schema(function (Get $get) {
                        return WdgSponsorTypeEnum::getFilamentBlocks($get('type'));
                    })
                    ->columnSpan(1)
                    ->hidden(fn (Get $get) => $get('type') == null)

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('cardinality')
                    ->label('order')
                    ->numeric()
                    ->sortable(),
                ViewColumn::make('preview')
                ->view('filament.forms.components.widgets.badge-table'),
                IconColumn::make('has_problem')
                ->boolean(),
                ToggleColumn::make('is_visible'),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->reorderable('cardinality')
            ->reorderRecordsTriggerAction(
                fn (Action $action, bool $isReordering) => $action
                    ->button()
                    ->label($isReordering ? 'Disable reordering' : 'Enable reordering'),
            )
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
            'index' => Pages\ListWdgSponsors::route('/'),
            'create' => Pages\CreateWdgSponsor::route('/create'),
            'edit' => Pages\EditWdgSponsor::route('/{record}/edit'),
        ];
    }
}
