<?php

namespace App\Console\Commands\v1;

use Exception;
use App\Models\Tag;
use App\Models\Post;
use App\Models\User;
use App\Models\Category;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

class ImportEntity extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    //protected $signature = 'import:legacy {entity?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import entity from audiofader.com/wp-json/wp/v2/{entity}';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $entityName = $this->argument('entity');
        if($entityName)
        {
            $this->handleEntity($entityName);
        }else{
            $entities = [
                'tags',
                'categories',
                'users',
                'posts',
            ];

            foreach($entities as $entityName)
            {
                $this->handleEntity($entityName);
            }
        }

        $this->info("Everything was imported");
        return Command::SUCCESS;
    }

    protected function handleEntity($entityName)
    {
        $page = 1;
        $keepIterating = true;
        $url = "https://www.audiofader.com/wp-json/wp/v2/{$entityName}";
        $totalEntities = 0;
        while ($keepIterating) {
            $qs = [
                'per_page' => 100,
                'page' => $page,
                'orderby' => 'date',
                'order' => 'asc',
            ];

            if($entityName !== 'posts')
            {
                unset($qs['orderby']);
                unset($qs['order']);
            }

            $res = Http::get($url, $qs)->json();

            if (count($res) === 0
                || (isset($res['code']) && $res['code'] === 'rest_post_invalid_page_number'))
            {
                $keepIterating = false;
                $this->info("Done, read {$totalEntities} {$entityName} 📚");
                continue;
            }

            if (isset($res['data']['status']) && $res['data']['status'] !== 200) {
                $this->error("Connecting to {$url} resulted in a {$res['data']['status']} error 😵");
                Log::error("Error with wp api", ['response' => $res]);
                return Command::FAILURE;
            }

            $countEntities= count($res);

            $this->info("Reading page {$page} 🕵️‍♂️...");
            $bar = $this->output->createProgressBar($countEntities);
            $bar->start();
            $this->writeEntities($entityName, $res, $bar);
            $bar->finish();
            $page++;
            $totalEntities += $countEntities;
            $this->line("");
            $this->line("Flipping page 📖");
        }
    }

    protected function writeEntities($entityName, $res, &$bar)
    {
        if($entityName === 'tags')
        {
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
            return;
        }

        if($entityName === 'categories')
        {
            foreach ($res as $category) {
                Category::firstOrCreate([
                    'legacy_id' => $category['id'],
                ], [
                    'description' => $category['description'],
                    'name' => $category['name'],
                    'slug' => $category['slug'],
                ]);

                $bar->advance();
            }
            return;
        }

        if($entityName === 'users')
        {
            $jsonUsers = collect(json_decode(file_get_contents('wp_users_202401241131.json')));

            foreach ($res as $user) {
                try{
                    $legacyUser = ($jsonUsers->where('ID', $user['id']))->values();

                    User::firstOrCreate([
                        'legacy_id' => $user['id'],
                    ], [
                        'password' => Hash::make(uniqid()),
                        'email' => $legacyUser[0]->user_email,
                        'description' => $user['description'],
                        'name' => $user['name'],
                        'slug' => $user['slug'],
                        'url' => $user['url'],
                        'display_name' => $legacyUser[0]->display_name,
                        'created_at' => $legacyUser[0]->user_registered,
                    ]);

                    $bar->advance();
                } catch (Exception $e)
                {
                    $this->error("Can't import {$user['id']} - {$user['name']}");
                    Log::error($e->getMessage(), ['user' => $legacyUser]);
                }
            }
            return;
        }

        if($entityName === 'posts')
        {
            foreach ($res as $post) {
                $post = Post::firstOrCreate([
                    'legacy_id' => $post['id'],
                ], [
                    'slug' => $post['slug'],
                    'created_at' => $post['date'],
                    'updated_at' => $post['modified'],
                    'status' => $post['status'],
                    'title' => $post['title']['rendered'],
                    'content' => $post['content']['rendered'],
                    'excerpt' => $post['excerpt']['rendered'],
                    'author_id' => User::where('legacy_id', $post['author'])->first()->id,
                ]);

                $legacyTags = Tag::query()
                    ->select('id')
                    ->whereIn('legacy_id', $post['tags'])
                    ->get()
                    ->pluck('id')
                    ->values();

                $legacyCategories = Category::query()
                    ->select('id')
                    ->whereIn('legacy_id', $post['categories'])
                    ->get()
                    ->pluck('id')
                    ->values();

                $post->tags()->sync($legacyTags);
                $post->categories()->sync($legacyCategories);

                $bar->advance();
            }
            return;
        }
    }
}
