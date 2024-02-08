<?php

namespace App\Console\Commands\v1;

use App\Models\Category;
use App\Models\Post;
use App\Models\Slug;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CleanSlugs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clean-slugs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean the slugs from dbs tables and put it in the sluggable table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        DB::beginTransaction();


        try {
            $categories = Category::query()->get(['id', 'slug', 'name']);
            foreach ($categories as $category) {
                if (empty($category->slug)) {
                    $category->slug = str_replace(' ', '-', $category->name);
                    $category->save();
                }
                $slug = new Slug(['name' => $category->slug]);
                $category->slug()->save($slug);
            }

            $posts = Post::query()->get(['id', 'slug', 'title']);
            foreach ($posts as $post) {
                if (empty($post->slug)) {
                    $post->slug = str_replace(' ', '-', $post->title);
                    $post->save();
                }
                $slug = new Slug(['name' => $post->slug]);
                $post->slug()->save($slug);
            }

            $users = User::query()->get(['id', 'slug', 'name']);
            foreach ($users as $user) {
                if (empty($user->slug)) {
                    $user->slug = str_replace(' ', '-', $user->name);
                    $user->save();
                }
                $validator = Validator::make(['slug' => $user->slug], [
                    'slug' => 'unique:slugs,name',
                ]);         
                if ($validator->fails()) {
                    $user->slug = $user->slug . '-' . Str::random(3);
                    $user->save();
                }

                $slug = new Slug(['name' => $user->slug]);
                $user->slug()->save($slug);
            }

            $tags = Tag::query()->get(['id', 'slug', 'name']);
            foreach ($tags as $tag) {
                if (empty($tag->slug)) {
                    $tag->slug = str_replace(' ', '-', $user->name);
                    $tag->save();
                }
                $validator = Validator::make(['slug' => $tag->slug], [
                    'slug' => 'unique:slugs,name',
                ]);         
                if ($validator->fails()) {
                    $tag->slug = $tag->slug . '-' . Str::random(3);
                    $tag->save();
                }

                $slug = new Slug(['name' => $tag->slug]);
                $tag->slug()->save($slug);
            }


            DB::commit();
            $this->info('The command was successful!');
            return Command::SUCCESS;
        } catch (\Exception $ex) {
            DB::rollBack();
            $this->error($ex->getMessage());
            return Command::FAILURE;
        }
    }
}
