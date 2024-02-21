<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Filament\Resources\PostResource;
use App\Models\Audio;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreatePost extends CreateRecord
{
    protected static string $resource = PostResource::class;

    // protected function handleRecordCreation(array $data): Model
    // {
    //     foreach ($data['json_content'] as $block) {
    //         if ($block['type'] == 'audio') {
    //             $audioData = $block['data']['audio'];
    //             $audio = Audio::create([
    //                 'disk' => $audioData['disk'],
    //                 'directory' => $audioData['directory'],
    //                 'name' => $audioData['name'],
    //                 'path' => $audioData['path'],
    //                 'type' => $audioData['type'],
    //                 'ext' => $audioData['ext'],
    //                 'title' => $block['data']['originalFilename'],
    //             ]);

    //             if ($audio) {
    //                 $block['data']['audio'] = $audio->id;
    //             }
    //         }
    //     }


    //     return static::getModel()::create($data);
    // }

    // protected function mutateFormDataBeforeCreate(array $data): array
    // {
    //     // foreach ($data['json_content'] as $key => $block) {
    //     //     if ($block['type'] == 'paragraph') {

    //     //         dd($block['data']);
    //     //     }
    //     // }
    //     // return $data;
    // }

    // protected function mutateFormDataBeforeCreate(array $data): array
    // {
    //     dd($data['json_content']);
    // }
}
