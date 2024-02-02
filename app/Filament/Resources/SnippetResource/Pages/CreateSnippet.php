<?php

namespace App\Filament\Resources\SnippetResource\Pages;

use App\Filament\Resources\SnippetResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSnippet extends CreateRecord
{
    protected static string $resource = SnippetResource::class;
}
