<?php

namespace App\Console\Commands\v1;

use Illuminate\Console\Command;

class ImportPosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:posts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import categories from audiofader.com/wp-json/wp/v2/posts';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
    }
}
