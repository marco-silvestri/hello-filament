<?php

namespace App\Filament\Resources\AudioResource\Pages;

use App\Filament\Resources\AudioResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateAudio extends CreateRecord
{
    protected static string $resource = AudioResource::class;


    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (blank($data['title'])) {
            $data['title'] = pathinfo($data['originalFilename'], PATHINFO_FILENAME);
        }

        $data['disk'] = $data['file']['disk'];
        $data['directory'] = $data['file']['directory'];
        $data['name'] = $data['file']['name'];
        $data['path'] = $data['file']['path'];
        $data['type'] = $data['file']['type'];
        $data['ext'] = $data['file']['ext'];

        unset($data['originalFilename']);

        return $data;
    }
}
