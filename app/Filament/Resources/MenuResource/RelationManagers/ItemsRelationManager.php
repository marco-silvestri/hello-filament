<?php

namespace App\Filament\Resources\MenuResource\RelationManagers;

use App\Enums\Cms\MenuOptionsEnum;
use App\Models\Category;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Page;
use App\Models\Tag;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->string(),
                Select::make('parent_id')
                    ->live()
                    ->options(function (MenuItem $record) {
                        return $record->menu->items()->where('has_submenu', true)->pluck('name', 'id');
                    }),
                TextInput::make('order')
                    ->numeric()
                    ->hidden(fn (Get $get): bool => $get('parent_id') ?? false)
                    ->minValue(1)
                    ->maxValue(function (MenuItem $record) {
                        return ($record->menu->items()->max('order') + 1);
                    })->default(1),
                Toggle::make('has_submenu')
                    ->live()
                    ->columnSpanFull()
                    ->onColor('success')
                    ->offColor('danger'),
                Select::make('type')
                    ->required()
                    ->live()
                    ->hidden(fn (Get $get): bool => $get('has_submenu') ?? false)
                    ->options(MenuOptionsEnum::class),
                TextInput::make('value')
                    ->required()
                    ->label('url')
                    ->url()
                    ->required()
                    ->hidden(fn (Get $get): bool => !($get('type') == MenuOptionsEnum::EXTERNAL_URL->value)),
                Select::make('value')
                    ->required()
                    ->label('Category')
                    ->options(Category::all()->pluck('name', 'id'))
                    ->required()
                    ->searchable()
                    ->hidden(fn (Get $get): bool => !($get('type') == MenuOptionsEnum::CATEGORY->value)),
                Select::make('value')
                    ->required()
                    ->label('Tag')
                    ->options(Tag::all()->pluck('name', 'id'))
                    ->required()
                    ->searchable()
                    ->hidden(fn (Get $get): bool => !($get('type') == MenuOptionsEnum::TAG->value)),
                Select::make('value')
                    ->required()
                    ->label('Page')
                    ->options(Page::all()->pluck('title', 'id'))
                    ->required()
                    ->hidden(fn (Get $get): bool => !($get('type') == MenuOptionsEnum::PAGE->value)),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->defaultGroup('parent.name')
            ->defaultSort('order')
            ->poll('10s')
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('order')
                    ->sortable(),
                IconColumn::make('has_submenu')
                    ->boolean(),
                TextColumn::make('type')
                    ->placeholder('-'),
                TextColumn::make('value')
                    ->placeholder('-')
                    ->formatStateUsing(function (string $state, MenuItem $record) {
                        if ($record->type == MenuOptionsEnum::PAGE->value) {
                            return Page::find($state)->name;
                        }
                        if ($record->type == MenuOptionsEnum::EXTERNAL_URL->value) {
                            return $state;
                        } 
                        if ($record->type == MenuOptionsEnum::CATEGORY->value) {
                            return Category::find($state)->name;
                        } 
                        if ($record->type == MenuOptionsEnum::TAG->value) {
                            return Tag::find($state)->name;
                        } 
                    }),
            ])
            ->filters([
                //
            ])
            // ->headerActions([
            //     Tables\Actions\CreateAction::make(),
            // ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->mutateFormDataUsing(function (MenuItem $record, array $data) {
                        if (isset($data['order'])) {
                            self::orderItems($record, $data['order']);
                        }
                        if ($data['has_submenu']) {
                            $data['type'] = null;
                            $data['value'] = null;
                        }
                        self::removeOrphanChildren($record);
                        return $data;
                    }),
                Tables\Actions\DeleteAction::make()->after(function (Model $record) {
                    $record->childrens()->delete();
                }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    private static function orderItems(MenuItem $record, int $order)
    {
        $itemsOrderToChange = MenuItem::query()->where('order', '>=', $order)->where('menu_id', $record->menu_id)->get();
        $isPresentSameOrder = MenuItem::query()->where('order', $order)->where('menu_id', $record->menu_id)->where('id', '!=', $record->id)->first();
        if ($isPresentSameOrder) {
            foreach ($itemsOrderToChange as $item) {
                $item->update([
                    'order' => $item->order + 1
                ]);
            }
        }
    }

    private static function removeOrphanChildren(MenuItem $record)
    {
        if ($record->has_submenu) {
            $record->childrens()->delete();
        }
    }
}
