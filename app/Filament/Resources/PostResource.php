<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Post;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use FilamentTiptapEditor\TiptapEditor;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use App\Filament\Resources\PostResource\Pages;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('author_id')
                    ->relationship('author', 'name')
                    ->native(false)
                    ->required(),
                Select::make('categories')
                    ->relationship(name: 'categories', titleAttribute: 'name')
                    ->multiple()
                    ->searchable()
                    ->preload(),
                Select::make('tags')
                    ->relationship(name: 'tags', titleAttribute: 'name')
                    ->multiple()
                    ->searchable()
                    ->preload(),
                Textarea::make('title')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                TiptapEditor::make('content'),
                    //->required()
                    //->maxLength(16777215)
                    //->columnSpanFull(),
                Textarea::make('excerpt')
                    ->required()
                    ->maxLength(16777215)
                    ->columnSpanFull(),
                Textarea::make('slug')
                    ->required()
                    ->maxLength(65535)
                    ->columnSpanFull(),
                Textarea::make('status')
                    ->required()
                    ->maxLength(65535)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('author.name')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('title')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('slug'),
                TextColumn::make('tags.name')
                    ->badge(),
                TextColumn::make('categories.name')
                    ->badge(),
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
                SelectFilter::make('tags')
                    ->relationship('tags', 'name')
                    ->multiple()
                    ->preload(),
                SelectFilter::make('categories')
                    ->relationship('categories', 'name')
                    ->multiple()
                    ->preload()
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
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
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'view' => Pages\ViewPost::route('/{record}'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
