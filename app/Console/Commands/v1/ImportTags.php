<?php

namespace App\Console\Commands\v1;

use App\Models\Tag;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Symfony\Component\Console\Command\Command as CommandCommand;

class ImportTags extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:tags';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import tags from audiofader.com/wp-json/wp/v2/tags';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $page = 1;
        $keepIterating = true;
        $url = "https://www.audiofader.com/wp-json/wp/v2/tags";
        $totalTags = 0;

        while ($keepIterating) {
            $res = Http::get($url, [
                'per_page' => 100,
                'page' => $page,
            ])->json();

            if(count($res) === 0){
                $keepIterating = false;
                $this->info("Done, read {$totalTags} tags 📚");
                continue;
            }

            if (isset($res['data']['status']) && $res['data']['status'] !== 200) {
                $this->error("Connecting to {$url} resulted in a {$res['data']['status']} error 😵");
                return Command::FAILURE;
            }

            $countTags = count($res);

            $this->info("Reading page {$page} 🕵️‍♂️...");
            $bar = $this->output->createProgressBar($countTags);
            $bar->start();
            foreach ($res as $tag) {
                Tag::firstOrCreate([
                    'legacy_id' => $tag['id'],
                ], [
                    'description' => $tag['description'],
                    'name' => $tag['name'],
                    'slug' => $tag['slug'],
                ]);

                $bar->advance();
            }

            $bar->finish();
            $page++;
            $totalTags += $countTags;
            $this->line("");
            $this->line("Flipping page 📖");
        }
    }
}
