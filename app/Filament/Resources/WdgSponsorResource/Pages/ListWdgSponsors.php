<?php

namespace App\Filament\Resources\WdgSponsorResource\Pages;

use App\Filament\Resources\WdgSponsorResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWdgSponsors extends ListRecords
{
    protected static string $resource = WdgSponsorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
