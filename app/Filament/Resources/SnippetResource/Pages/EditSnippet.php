<?php

namespace App\Filament\Resources\SnippetResource\Pages;

use App\Enums\HookEnum;
use Filament\Actions;
use App\Traits\Cms\HasCaching;
use App\Services\SnippetService;
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
                fn() => SnippetService::flushSnippetsCache(),
                fn() => SnippetService::fillSnippetsCache()
            ),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->update($data);

        SnippetService::refreshSnippetsCache(HookEnum::getByValue($record->hook));

        return $record;
    }
}
