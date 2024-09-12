<?php

namespace App\Filament\Resources\PostResource\Pages;

use DOMDocument;
use App\Models\Post;
use App\Models\Contact;
use App\Models\Cms\Sponsor;
use Filament\Actions\Action;
use App\Models\Communication;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use App\Traits\Cms\HasJsonOperations;
use Filament\Forms\Components\Select;
use Illuminate\Support\Facades\Cache;
use Filament\Forms\Components\Textarea;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\PostResource;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use Filament\Forms\Components\RichEditor;
use App\Enums\Cms\CommunicationStatusEnum;

class EditPost extends EditRecord
{
    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('scheduled')
                ->label(__('posts.lbl-scheduled-send'))
                ->form([
                    Select::make('recipients')
                        ->label(__('scheduled-send.lbl-recipients'))
                        ->multiple()
                        ->options(function () {
                            $contacts = Contact::get();
                            $contacts = $contacts->map(function ($contact) {
                                $label = "$contact->name ($contact->email)";
                                if ($contact->sponsor) {
                                    $label = "{$contact->sponsor->name} - $label";
                                };

                                $contact->label = $label;

                                return $contact;
                            })->pluck('label', 'id');

                            return $contacts;
                        })
                        ->required()
                        ->createOptionForm([
                            TextInput::make('name')
                                ->required(),
                            Select::make('company')
                                ->options(Sponsor::select('name', 'id')
                                    ->get()
                                    ->pluck('name', 'id'))
                                ->required(),
                            TextInput::make('email')
                                ->required()
                                ->unique()
                                ->regex('/^.+@.+$/i')
                                ->email(),
                        ])->createOptionUsing(function (array $data) {
                            $contact = Contact::create([
                                'name' => $data['name'],
                                'sponsor_id' => $data['company'],
                                'email' => $data['email'],
                            ]);

                            return $contact->id;
                        }),
                    TextInput::make('subject')
                        ->label(__('scheduled-send.lbl-subject'))
                        ->default(fn (Post $record) =>
                        __('scheduled-send.fl-default-subject', [
                            'title' => $record->title,
                        ]))
                        ->required(),
                    RichEditor::make('body')
                        ->label(__('scheduled-send.lbl-body'))
                        ->default(fn (Post $record) =>
                        __('scheduled-send.fl-default-body', [
                            'title' => $record->title,
                            'link' => $record->encoded_url,
                        ]))
                        ->required(),
                ])
                ->action(function (Post $record, array $data): void {
                    $communication = Communication::create([
                        'subject' => $data['subject'],
                        'body' => $data['body'],
                        'status' => CommunicationStatusEnum::SCHEDULED,
                        'post_id' => $record->id,
                    ]);

                    $communication->contacts()->sync($data['recipients']);
                }),
            Action::make('preview')
                ->label(__('posts.lbl-preview'))
                ->url(fn ($record): string => route('preview', ['post' => $record]))
                ->openUrlInNewTab(),
            Action::make('save_top')
                ->action('save')
                ->label(__('common.btn-save')),
            Action::make('cancel')
                ->label(__('common.btn-cancel'))
                ->url($this->getResource()::getUrl('index')),
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $data['content'] = HasJsonOperations::extractMeaningfulContent($data['json_content']);
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

    protected function getFormActions(): array
    {
        return [];
    }
}
