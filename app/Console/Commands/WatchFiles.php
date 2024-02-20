<?php

namespace App\Console\Commands;

use Awcodes\Curator\Models\Media;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Spatie\Watcher\Watch;

class WatchFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:watch-files';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Watch::path('storage/app/public/media')
            ->onFileCreated(function (string $newFilePath) {
                try {
                    $this->info( "Adding new file: " . $newFilePath);
                    $pathStripped = str_replace('storage/app/public/', '', $newFilePath);
                    $existingMedia = Media::query()->where('path', $pathStripped)->first();
                    if (!$existingMedia) {
                        $fileName = strstr(str_replace('media/','', $pathStripped), '.', true);
                        $file = Storage::disk('public')->path($pathStripped);
                        $image = Image::make($file);
                        $media = Media::create([
                            'disk' => 'public',
                            'directory' => 'media',
                            'visibility' => 'public',
                            'name' => $fileName,
                            'path' => $pathStripped,
                            'width' => $image->width(),
                            'height' => $image->height(),
                            'size' => Storage::disk('public')->size($pathStripped),
                            'type' => Storage::disk('public')->mimeType($pathStripped),
                            'ext' => pathinfo($file, PATHINFO_EXTENSION),
                            'title' => $fileName
                        ]);

                        if ($media) {
                            $this->info("media added with id: " . $media->id);
                        }
                    } else {
                        $this->info("file already exists in media");
                    }
                } catch (\Exception $ex) {
                    Log::error('Watchfiles command error', ['error' => $ex->getMessage()]);
                    $this->error($ex->getMessage());
                }
            })
            ->start();
    }
}
