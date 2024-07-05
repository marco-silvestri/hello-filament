<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Hash;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Filters\SelectFilter;
use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Settings';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('roles')
                    ->label(__('common.lbl-roles'))
                    ->relationship(name: 'roles', titleAttribute: 'name')
                    ->multiple()
                    ->searchable(),
                TextInput::make('name')
                    ->label(__('common.fld-name'))
                    ->required()
                    ->live(debounce: 500)
                    ->afterStateUpdated(function (?string $state, ?string $old, Set $set) {
                        $set('slug.name', Str::of($state)->slug());
                    })
                    ->maxLength(255),
                Section::make()
                    ->relationship('slug')
                    ->schema([
                        TextInput::make('name')
                            ->label(__('common.lbl-slug'))
                            ->required()
                            ->readonly()
                            ->unique(table: 'slugs', column: 'name', ignoreRecord: true),
                    ]),
                TextInput::make('email')
                    ->label(__('common.fld-email'))
                    ->email()
                    ->required()
                    ->maxLength(255),
                TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->revealable()
                    ->dehydrateStateUsing(fn($state) => Hash::make($state))
                    ->dehydrated(fn($state) => filled($state))
                    ->required(fn(string $context): bool => $context === 'create')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label(__('common.fld-name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('roles.name')
                    ->label(__('common.lbl-roles'))
                    ->searchable()
                    ->badge(),
                Tables\Columns\TextColumn::make('email')
                    ->label(__('common.fld-email'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('common.fld-created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('common.fld-updated-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('roles')
                    ->label(__('common.lbl-roles'))
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload()
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                ])
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getModelLabel(): string
    {
        return __('common.ent-user');
    }

    public static function getPluralModelLabel(): string
    {
        return __('common.ent-users');
    }
}
