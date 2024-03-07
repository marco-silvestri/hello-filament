<?php

namespace App\Console\Commands;

use App\Models\WdgSponsor;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class DeleteOrphanFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:delete-orphan-files';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete orphan file across the mapped folder';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('deleting orphan files');
        $activeImg = [];
        $sponsors = WdgSponsor::pluck('json_content')->toArray();
        foreach ($sponsors as $sponsor) {
            $activeImg[] = $sponsor['img'];
        }
        collect(Storage::disk('public')->allFiles('sponsor'))
            ->reject(fn (string $file) => in_array($file, $activeImg))
            ->each(fn ($file) => Storage::disk('public')->delete($file));
        $this->info('done');
    }
}
