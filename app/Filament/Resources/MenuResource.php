<?php

namespace App\Filament\Resources;

use App\Enums\Cms\MenuOptionsEnum;
use App\Filament\Resources\MenuResource\Pages;
use App\Filament\Resources\MenuResource\RelationManagers\ItemsRelationManager;
use App\Models\Category;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Page;
use App\Models\Tag;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder as EBuilder;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Support\Enums\VerticalAlignment;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Factories\Relationship;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class MenuResource extends Resource
{
    protected static ?string $model = Menu::class;

    protected static ?string $navigationIcon = 'heroicon-s-table-cells';

    protected static ?string $navigationGroup = 'Settings';

    public static function form(Form $form): Form
    {

        return $form
            ->schema([
                TextInput::make('name')
                    ->string(),
                Actions::make([
                    Action::make('add_menu_item')
                        ->form([
                            TextInput::make('name')
                                ->required()
                                ->string(),
                            Select::make('parent_id')
                                ->live()
                                ->options(function (Menu $record) {
                                    return $record->items()->where('has_submenu', true)->pluck('name', 'id');
                                }),
                            TextInput::make('order')
                                ->numeric()
                                ->hidden(fn (Get $get): bool => $get('parent_id') ?? false)
                                ->minValue(1)
                                ->maxValue(function (Menu $record) {
                                    return ($record->items()->max('order') + 1);
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
                        ])
                        ->icon('heroicon-s-plus')
                        ->action(function (array $data, Menu $record) {
                            if (isset($data['order'])) {
                                self::orderItems($record, $data['order']);
                            }
                            $data['menu_id'] = $record->id;
                            MenuItem::create($data);
                        }),
                ])
                    ->verticalAlignment(VerticalAlignment::End)
                    ->hiddenOn('create'),
                ViewField::make('rating')
                    ->columnSpanFull()
                    ->view('filament.forms.components.menu-preview')
                    ->hiddenOn('create'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
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
                // Filter::make('submenu')
                //     ->query(fn (EBuilder $query): EBuilder => $query->where('submenu', true))
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            ItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMenus::route('/'),
            'create' => Pages\CreateMenu::route('/create'),
            'view' => Pages\ViewMenu::route('/{record}'),
            'edit' => Pages\EditMenu::route('/{record}/edit'),
        ];
    }

    private static function orderItems(Menu $record, int $order)
    {
        $itemsOrderToChange = $record->items()->where('order', '>=', $order)->get();
        $isPresentSameOrder = $record->items()->where('order', $order)->first();
        if ($isPresentSameOrder) {
            foreach ($itemsOrderToChange as $item) {
                $item->update([
                    'order' => $item->order + 1
                ]);
            }
        }
    }
}
