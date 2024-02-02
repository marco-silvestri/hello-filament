<?php

namespace App\Filament\Resources\SnippetResource\Pages;

use Filament\Actions;
use App\Services\SnippetService;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\SnippetResource;

class ListSnippets extends ListRecords
{
    protected static string $resource = SnippetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->after(
                fn() => SnippetService::flushSnippetsCache(),
                fn() => SnippetService::fillSnippetsCache()
            ),
        ];
    }
}
