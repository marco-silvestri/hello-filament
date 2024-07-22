<?php

namespace App\Filament\Resources;

use Exception;
use Filament\Forms;
use Filament\Tables;
use App\Models\Comment;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Log;
use App\Enums\Cms\CommentStatusEnum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Notifications\Notification;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use App\Tables\Columns\Cms\CommentStatusColumn;
use App\Filament\Resources\CommentResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\CommentResource\RelationManagers;

class CommentResource extends Resource
{
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', CommentStatusEnum::AWAITING_MODERATION->value)->count();
    }

    protected static ?string $model = Comment::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationGroup = 'Contents';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->numeric()
                    ->label(__('common.fld-id')),
                TextColumn::make('post.title')
                    ->sortable()
                    ->label(__('common.fld-post')),
                TextColumn::make('author.name')
                    ->sortable()
                    ->label(__('common.fld-author'))
                    ->state(fn($record) => $record->author ? $record->author->name : __('comments.lbl-anonymous')),
                TextColumn::make('body')
                    ->limit(30)
                    ->label(__('common.fld-body')),
                TextColumn::make('status')
                    ->badge()
                    ->sortable()
                    ->label(__('common.fld-status')),
                TextColumn::make('created_at')
                    ->badge()
                    ->sortable()
                    ->dateTime()
                    ->label(__('common.fld-created-at'))
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                SelectFilter::make('status')
                    ->options(CommentStatusEnum::class)
            ])
            ->actions([
                Action::make('moderate-comment')
                    ->modalContent(fn (Action $action, Comment $record): View => view(
                        'cms.filament.comment-moderation',
                        [
                            'record' => $record,
                            'action' => $action,
                        ],
                    ))
                    ->label(__('comments.act-moderate-comment'))
                    ->registerModalActions([
                        Action::make('approve')
                            ->action(function (Comment $record) {
                                try {
                                    $record->update([
                                        'status' => CommentStatusEnum::APPROVED->value,
                                        'status_changed_at' => now(),
                                    ]);

                                    Notification::make()
                                        ->title(__('comments.ntf-approved'))
                                        ->success()
                                        ->send();
                                } catch (Exception $e) {
                                    Log::error($e->getMessage());
                                    Notification::make()
                                        ->title(__('comments.ntf-err-approved'))
                                        ->danger()
                                        ->send();
                                }
                            })
                            ->label(__('comments.act-approve'))
                            ->requiresConfirmation()
                            ->cancelParentActions()
                            ->color('success')
                            ->disabled(fn (Comment $record) => $record->status === CommentStatusEnum::APPROVED->value),
                        Action::make('reject')
                            ->action(function (Comment $record) {
                                try {
                                    $record->update([
                                        'status' => CommentStatusEnum::REJECTED->value,
                                        'status_changed_at' => now(),
                                    ]);

                                    Notification::make()
                                        ->title(__('comments.ntf-rejected'))
                                        ->success()
                                        ->send();
                                } catch (Exception $e) {
                                    Log::error($e->getMessage());
                                    Notification::make()
                                        ->title(__('comments.ntf-err-rejected'))
                                        ->danger()
                                        ->send();
                                }
                            })
                            ->label(__('comments.act-reject'))
                            ->requiresConfirmation()
                            ->cancelParentActions()
                            ->color('danger')
                            ->disabled(fn (Comment $record) => $record->status === CommentStatusEnum::REJECTED->value),
                        Action::make('put-back-to-moderation')
                            ->action(function (Comment $record) {
                                try {
                                    $record->update([
                                        'status' => CommentStatusEnum::AWAITING_MODERATION->value,
                                        'status_changed_at' => now(),
                                    ]);

                                    Notification::make()
                                        ->title(__('comments.ntf-awaiting'))
                                        ->success()
                                        ->send();
                                } catch (Exception $e) {
                                    Log::error($e->getMessage());
                                    Notification::make()
                                        ->title(__('comments.ntf-err-awaiting'))
                                        ->danger()
                                        ->send();
                                }
                            })
                            ->label(__('comments.act-back-to-moderation'))
                            ->requiresConfirmation()
                            ->cancelParentActions()
                            ->color('warning')
                            ->disabled(fn (Comment $record) => $record->status === CommentStatusEnum::AWAITING_MODERATION->value),
                    ])
                    ->modalSubmitAction(false)
                    ->modalCancelAction(false)
                    ->modalFooterActions()
                    ->slideOver(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageComments::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
