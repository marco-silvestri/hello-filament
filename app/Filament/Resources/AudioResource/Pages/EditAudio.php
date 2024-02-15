<?php

namespace App\Filament\Resources\AudioResource\Pages;

use App\Filament\Resources\AudioResource;
use App\Models\Audio;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class EditAudio extends EditRecord
{
    protected static string $resource = AudioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }


    protected function handleRecordUpdate(Model $record, array $data): Model
    {

        if (blank($data['title'])) {
            $data['title'] = pathinfo($data['originalFilename'], PATHINFO_FILENAME);
        }

        if (isset($data['file'])) {
            $data['disk'] = $data['file']['disk'];
            $data['directory'] = $data['file']['directory'];
            $data['name'] = $data['file']['name'];
            $data['path'] = $data['file']['path'];
            $data['type'] = $data['file']['type'];
            $data['ext'] = $data['file']['ext'];
        }

        unset($data['originalFilename']);
        Storage::disk($record->disk)->delete($record->path);

        $record->update($data);
        return $record;
    }
}
