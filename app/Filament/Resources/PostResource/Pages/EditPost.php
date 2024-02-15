<?php

namespace App\Filament\Resources\PostResource\Pages;

use Filament\Actions;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\PostResource;
use App\Models\Audio;
use Filament\Resources\Pages\EditRecord;

use function PHPUnit\Framework\isNull;

class EditPost extends EditRecord
{

    protected static string $resource = PostResource::class;

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
        $key = $record->getTable();
        Cache::forget("{$key}-{$record->slug}");

        return $record;
    }
}
