<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Enums\Cms\HookEnum;
use App\Models\Snippet;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Illuminate\Support\Facades\Cache;
use PhpParser\Node\Expr\Cast\Object_;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Actions\Action;
use App\Filament\Resources\SnippetResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\SnippetResource\RelationManagers;
use App\Services\SnippetService;
use Wiebenieuwenhuis\FilamentCodeEditor\Components\CodeEditor;

class SnippetResource extends Resource
{
    protected static ?string $model = Snippet::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required(),
                TextInput::make('description'),
                CodeEditor::make('payload')
                    ->label('Snippet')
                    ->required(),
                Select::make('hook')
                    ->options([
                        HookEnum::HEAD->value => HookEnum::HEAD->getLabel(),
                        HookEnum::BODY->value => HookEnum::BODY->getLabel(),
                        HookEnum::FOOTER->value => HookEnum::FOOTER->getLabel(),
                    ])
                    ->native(false)
                    ->required(),
                Select::make('status')
                        ->options([
                            true => 'Enabled',
                            false => 'Disabled',
                        ])
                        ->native(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->sortable(),
                TextColumn::make('payload'),
                TextColumn::make('description'),
                TextColumn::make('hook')
                    ->badge()
                    ->sortable(),
                TextColumn::make('priority')
                    ->sortable(),
                IconColumn::make('status')
                    ->icon(fn (bool $state): string => match ($state) {
                        true => 'heroicon-o-check',
                        false => 'heroicon-o-x-mark',
                    })
                    ->color(fn (bool $state): string => match ($state) {
                        true => 'success',
                        false => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                    // ->after(
                    //     fn() => SnippetService::flushSnippetsCache(),
                    //     fn() => SnippetService::fillSnippetsCache()
                    // ),
                Tables\Actions\DeleteAction::make()
                    // ->after(
                    //     fn() => SnippetService::flushSnippetsCache(),
                    //     fn() => SnippetService::fillSnippetsCache()
                    // ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->reorderable('priority')
            ->defaultGroup('hook');
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
            'index' => Pages\ListSnippets::route('/'),
            'create' => Pages\CreateSnippet::route('/create'),
            'edit' => Pages\EditSnippet::route('/{record}/edit'),
        ];
    }
}
