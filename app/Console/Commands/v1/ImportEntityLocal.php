<?php

namespace App\Console\Commands\v1;

use Exception;
use App\Models\Tag;
use App\Models\Post;
use App\Models\User;
use App\Models\Category;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

class ImportEntityLocal extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:legacy {entity?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import entity from jsons';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $keysAndSources = [
            'posts' => [
                'key' => 'wp_posts',
                'source' => 'legacy-data/audio_fader_posts.json',
            ],
            'users' => [
                'key' => 'wp_users',
                'source' => 'legacy-data/audio_fader_users.json',
            ],
            'tags' => [
                'key' => 'wp_tags',
                'source' => 'legacy-data/audio_fader_tags.json',
            ],
            'categories' => [
                'key' => 'wp_categories',
                'source' => 'legacy-data/audio_fader_categories.json',
            ],
            'taxonomy' => [
                'key' => 'wp_term_relationships',
                'source' => 'legacy-data/audio_fader_taxonomy.json'
            ],
            'usermeta' => [
                'key' => 'wp_usermeta',
                'source' => 'legacy-data/audio_fader_user_description.json'
            ],
        ];

        $rawPosts = $this->collectWpJson($keysAndSources['posts']['source'], $keysAndSources['posts']['key']);
        $rawUsers =  $this->collectWpJson($keysAndSources['users']['source'], $keysAndSources['users']['key']);
        $rawCategories =  $this->collectWpJson($keysAndSources['categories']['source'], $keysAndSources['categories']['key']);
        $rawTags =  $this->collectWpJson($keysAndSources['tags']['source'], $keysAndSources['tags']['key']);
        $rawTaxonomy =  $this->collectWpJson($keysAndSources['taxonomy']['source'], $keysAndSources['taxonomy']['key']);
        $rawUserMeta =  $this->collectWpJson($keysAndSources['usermeta']['source'], $keysAndSources['usermeta']['key']);

        $rawUserMeta = $rawUserMeta->filter(function($meta){
            return $meta->meta_value != null;
        });
        $bar = $this->output->createProgressBar(count($rawPosts));
        $bar->start();
        $rawPosts->map(function($rawPost)
            use($rawUsers, $rawCategories, $rawTags, $rawTaxonomy, $rawUserMeta, &$bar)
            {
            try{
                DB::transaction(function () use($rawPost, $rawUsers, $rawCategories, $rawTags, $rawTaxonomy, $rawUserMeta){
                    $legacyUser = $rawUsers->where('ID', $rawPost->post_author)->values()[0];
                    $legacyTaxonomy = $rawTaxonomy->where('object_id', $rawPost->ID)->pluck('term_taxonomy_id')->toArray();
                    $legacyCategories = $rawCategories->whereIn('id', $legacyTaxonomy)->values();
                    $legacyTags = $rawTags->whereIn('id', $legacyTaxonomy)->values();
                    $legacyUserMeta = $rawUserMeta->where('user_id', $rawPost->post_author)->pluck('meta_value')->flatten()->toArray();

                    if(count($legacyUserMeta) > 0)
                    {
                        $legacyUserMeta = $legacyUserMeta[0];
                    }else{
                        $legacyUserMeta = null;
                    }

                    $categories = array_values($legacyCategories->map(function($legacyCategory){
                        $category = Category::firstOrCreate([
                            'legacy_id' => $legacyCategory->id,
                        ], [
                            'name' => $legacyCategory->name,
                            'slug' => $legacyCategory->slug,
                        ]);

                        return $category->id;
                    })->toArray());

                    $tags = array_values($legacyTags->map(function($legacyTag){
                        $tag = Tag::firstOrCreate([
                            'legacy_id' => $legacyTag->id,
                        ], [
                            'name' => $legacyTag->name,
                            'slug' => $legacyTag->slug,
                        ]);

                        return $tag->id;
                    })->toArray());

                    $user = User::firstOrCreate([
                        'legacy_id' => $legacyUser->ID,
                    ], [
                        'password' => Hash::make(uniqid()),
                        'email' => $legacyUser->user_email,
                        'name' => $legacyUser->user_login,
                        'slug' => $legacyUser->user_nicename,
                        'url' => $legacyUser->user_url,
                        'description' => $legacyUserMeta,
                        'display_name' => $legacyUser->display_name,
                        'created_at' => $legacyUser->user_registered,
                    ]);

                    $post = Post::firstOrCreate([
                        'legacy_id' => $rawPost->ID,
                    ], [
                        'slug' => $rawPost->post_name,
                        'created_at' => $rawPost->post_date_gmt,
                        'updated_at' => $rawPost->post_modified_gmt,
                        'status' => $rawPost->post_status,
                        'title' => $rawPost->post_title,
                        'content' => $rawPost->post_content,
                        'excerpt' => $rawPost->post_excerpt,
                        'author_id' => $user->id,
                    ]);

                    $post->tags()->sync($tags);
                    $post->categories()->sync($categories);
                });
            }catch(Exception $e)
            {
                $this->error("Cannot import {$rawPost->ID}");
                Log::error("Cannot import {$rawPost->ID}, error is {$e->getMessage()}", ['error'=>$e]);
            }
            $bar->advance();
        });

        $bar->finish();
        $this->info("Done!");
        return Command::SUCCESS;
    }

    protected function collectWpJson($source, $key):Collection
    {
        $rawJson = file_get_contents($source);
        $rawJson = preg_replace('/[[:cntrl:]]/', '', $rawJson);
        $collectedJSon = collect(json_decode($rawJson)->{$key});

        return $collectedJSon;
    }
}
