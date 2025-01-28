<?php

namespace App\Filament\Resources\TagResource\Pages;

use Filament\Actions;
use Illuminate\Support\Facades\Cache;
use App\Filament\Resources\TagResource;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\CreateRecord;

class CreateTag extends CreateRecord
{
    protected static string $resource = TagResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->update($data);
        Cache::flush();
        return $record;
    }
}
