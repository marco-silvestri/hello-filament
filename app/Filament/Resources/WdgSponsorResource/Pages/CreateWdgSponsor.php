<?php

namespace App\Filament\Resources\WdgSponsorResource\Pages;

use App\Filament\Resources\WdgSponsorResource;
use App\Models\WdgSponsor;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateWdgSponsor extends CreateRecord
{
    protected static string $resource = WdgSponsorResource::class;


    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['cardinality'] = WdgSponsor::query()->max('cardinality') + 1;

        return $data;
    }
}
