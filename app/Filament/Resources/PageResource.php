<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PageResource\Pages;
use App\Filament\Resources\PageResource\RelationManagers;
use App\Models\Page;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Forms;
use Filament\Forms\Components\Builder as ComponentsBuilder;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use FilamentTiptapEditor\TiptapEditor;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Settings';

    public static function form(Form $form): Form
    {
        return $form
            ->columns(3)
            ->schema([
                Fieldset::make()
                    ->columnSpan(2)
                    ->schema([
                        ComponentsBuilder::make('blocks')
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
                                    ]),
                                Block::make('paragraph')
                                    ->icon('heroicon-m-bars-3-bottom-left')
                                    ->schema([
                                        TiptapEditor::make('content')
                                            ->label('')
                                            ->profile('minimal')
                                            ->columnSpanFull()
                                            ->disableBubbleMenus()
                                            ->disableFloatingMenus(),
                                    ]),
                                Block::make('image')
                                    ->icon('heroicon-o-photo')
                                    ->schema([
                                        CuratorPicker::make('image')
                                    ]),
                                Block::make('video')
                                    ->schema([
                                        TextInput::make('url')
                                            ->label('Video Url')
                                            ->required(),
                                    ]),
                            ]),
                    ]),
                Fieldset::make()
                    ->columnSpan(1)
                    ->schema([
                        Placeholder::make('url')
                            ->columnSpanFull()
                            ->label('Url')
                            ->content(fn (Page $record): string => '/' . $record->slug->name),
                        TextInput::make('title')
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
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable(),
                TextColumn::make('slug.name')
                    ->label('Url')
                    ->prefix('/')
                    ->searchable(),
                TextColumn::make('layout')
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
            'index' => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'edit' => Pages\EditPage::route('/{record}/edit'),
        ];
    }
}
