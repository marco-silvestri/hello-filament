<?php

namespace App\Filament\Resources\PostResource\Pages;

use Filament\Actions;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\PostResource;
use App\Models\Audio;
use DOMDocument;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;

use function PHPUnit\Framework\isNull;

class EditPost extends EditRecord
{

    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->update($data);
        $key = $record->getTable();
        Cache::forget("{$key}-{$record->slug}");

        foreach ($data['json_content'] as $key => $block) {
            if ($block['type'] == 'paragraph') {

                // Crea un nuovo oggetto DOMDocument
                $dom = new DOMDocument();

                // Carica l'HTML
                $dom->loadHTML($block['data']['content']);

                // Seleziona tutti i tag <img>
                $images = $dom->getElementsByTagName('img');

                // Itera su ogni tag <img> trovato
                foreach ($images as $image) {
                    // Recupera il valore dell'attributo src
                    $src = $image->getAttribute('src');

                    // dd($src);
                    // dd(Storage::disk('public')->size('media/5cc8b5f9-43d1-4234-9419-36a523a0347a.png'));
                }
            }
        }
        return $record;
    }
}
