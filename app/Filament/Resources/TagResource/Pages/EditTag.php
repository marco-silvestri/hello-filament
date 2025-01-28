<?php

namespace App\Filament\Resources\TagResource\Pages;

use Filament\Actions;
use Illuminate\Support\Facades\Cache;
use App\Filament\Resources\TagResource;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\EditRecord;

class EditTag extends EditRecord
{
    protected static string $resource = TagResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->update($data);
        Cache::flush();
        return $record;
    }
}
