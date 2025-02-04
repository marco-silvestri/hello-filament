<?php

namespace App\Filament\Resources\SnippetResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\SnippetResource;
use Illuminate\Support\Facades\Cache;

class ListSnippets extends ListRecords
{
    protected static string $resource = SnippetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->after(
                    fn() => Cache::forget("snippets"),
                ),
        ];
    }
}
