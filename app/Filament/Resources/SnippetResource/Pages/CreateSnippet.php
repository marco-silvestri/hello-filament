<?php

namespace App\Filament\Resources\SnippetResource\Pages;

use App\Filament\Resources\SnippetResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class CreateSnippet extends CreateRecord
{
    protected static string $resource = SnippetResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $model = static::getModel()::create($data);

        Cache::forget("snippets");

        return $model;
    }
}
