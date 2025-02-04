<?php

namespace App\Filament\Resources\SnippetResource\Pages;

use Filament\Actions;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\SnippetResource;

class EditSnippet extends EditRecord
{

    protected static string $resource = SnippetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->after(
                    fn() => Cache::forget("snippets"),
                ),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->update($data);

        Cache::forget("snippets");

        return $record;
    }
}
