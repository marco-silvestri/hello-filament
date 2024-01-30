<?php

namespace App\Actions;

use Illuminate\Support\Str;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\ComponentContainer;
use FilamentTiptapEditor\TiptapEditor;
use Filament\Forms\Components\Checkbox;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\BaseFileUpload;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Awcodes\Curator\PathGenerators\DatePathGenerator;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class MediaAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'filament_tiptap_media';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->arguments([
                'src' => '',
                'alt' => '',
                'title' => '',
                'width' => '',
                'height' => '',
                'lazy' => null,
            ])
            ->modalWidth('md')
            ->mountUsing(function (TiptapEditor $component, ComponentContainer $form, array $arguments) {
                $source = $arguments['src'] !== ''
                    ? $component->getDirectory() . Str::of($arguments['src'])
                    ->after($component->getDirectory())
                    : null;

                $form->fill([
                    'src' => $source,
                    'alt' => $arguments['alt'] ?? '',
                    'title' => $arguments['title'] ?? '',
                    'width' => $arguments['width'] ?? '',
                    'height' => $arguments['height'] ?? '',
                    'lazy' => $arguments['lazy'] ?? false,
                ]);
            })->modalHeading(function (TiptapEditor $component, array $arguments) {
                $context = blank($arguments['src'] ?? null) ? 'insert' : 'update';

                return trans('filament-tiptap-editor::media-modal.heading.' . $context);
            })->form(function (TiptapEditor $component) {
                return [
                    CuratorPicker::make('Image')
                        ->label("Image to upload")
                        ->buttonLabel('upload')
                        ->color('primary') // defaults to primary
                        ->outlined() // defaults to true
                        ->size('md') // defaults to md
                        ->constrained() // defaults to false (forces image to fit inside the preview area)
                        ->pathGenerator(DatePathGenerator::class) // see path generators below
                        ->lazyLoad() // defaults to true
                        ->listDisplay() // defaults to true
                        ->tenantAware() // defaults to true
                        ->defaultPanelSort() // defaults to 'desc'
                        // see https://filamentphp.com/docs/2.x/forms/fields#file-upload for more information about the following methods
                        ->preserveFilenames()
                        ->multiple() // required if using a relationship with multiple media
                        //->relationship(string $relationshipName, string 'titleColumnName')
                        ->orderColumn('order') // only necessary to rename the order column if using a relationship with multiple media
                ];
            })->action(function (TiptapEditor $component, $data) {
                if (config('filament-tiptap-editor.use_relative_paths')) {
                    $source = Str::of($data['src'])
                        ->replace(config('app.url'), '')
                        ->ltrim('/')
                        ->prepend('/');
                } else {
                    $source = str_starts_with($data['src'], 'http')
                        ? $data['src']
                        : Storage::disk(config('filament-tiptap-editor.disk'))->url($data['src']);
                }

                $component->getLivewire()->dispatch(
                    'insert-content',
                    type: 'media',
                    statePath: $component->getStatePath(),
                    media: [
                        'src' => $source,
                        'alt' => $data['alt'] ?? null,
                        'title' => $data['title'],
                        'width' => $data['width'],
                        'height' => $data['height'],
                        'lazy' => $data['lazy'] ?? false,
                        'link_text' => $data['link_text'] ?? null,
                    ],
                );
            });
    }
}
