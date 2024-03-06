<?php

namespace App\Console\Commands\v1;

use Exception;
use Carbon\Carbon;
use App\Models\Tag;
use App\Models\Post;
use App\Models\User;
use App\Models\Media;
use App\Enums\RoleEnum;
use App\Models\Profile;
use App\Models\Category;
use Illuminate\Support\Str;
use App\Traits\Cms\HasWpData;
use Illuminate\Console\Command;
use App\Enums\Cms\PostStatusEnum;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class ImportEntityLocal extends Command
{
    use HasWpData;
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

    private $oldPermissions = [
        'customer' => RoleEnum::CUSTOMER->value,
        'author' => RoleEnum::AUTHOR->value,
        'editor' => RoleEnum::EDITOR->value,
        'contributor' => RoleEnum::EDITOR->value,
        'permesso_modifica_template' => RoleEnum::EDITOR->value,
        'wpseo_editor' => RoleEnum::EDITOR->value,
        'wpseo_manager' => RoleEnum::EDITOR->value,
        'superadmin' => RoleEnum::SUPERADMIN->value,
        'user' => RoleEnum::CUSTOMER->value,
        'subscriber' => RoleEnum::CUSTOMER->value,
        'administrator' => RoleEnum::ADMIN->value,
        'bbp_participant' => RoleEnum::ADMIN->value,
        'adrotate_ad_delete' => RoleEnum::ADMIN->value,
        'adrotate_ad_manage' => RoleEnum::ADMIN->value,
        'adrotate_group_delete' => RoleEnum::ADMIN->value,
        'adrotate_group_manage' => RoleEnum::ADMIN->value,
        'create_pages' => RoleEnum::ADMIN->value,
        'create_posts' => RoleEnum::ADMIN->value,
        'bbp_keymaster'=> RoleEnum::ADMIN->value,
        'bbp_moderator' => RoleEnum::ADMIN->value,
        'shop_manager' => RoleEnum::ADMIN->value,
        'edit_product' => RoleEnum::ADMIN->value,
        'edit_products' => RoleEnum::ADMIN->value,
        'edit_published_products' => RoleEnum::ADMIN->value,
        'publish_products' => RoleEnum::ADMIN->value,
        'activate_plugins' => RoleEnum::ADMIN->value,
        'assign_product_terms' => RoleEnum::ADMIN->value,
        'assign_shop_coupon_terms' => RoleEnum::ADMIN->value,
        'assign_shop_order_terms' => RoleEnum::ADMIN->value,
        'assign_shop_webhook_terms' => RoleEnum::ADMIN->value,
        'create_roles' => RoleEnum::ADMIN->value,
        'delete_membership_plan' => RoleEnum::ADMIN->value,
        'delete_others_products' => RoleEnum::ADMIN->value,
        'delete_plugins' => RoleEnum::ADMIN->value,
        'delete_roles' => RoleEnum::ADMIN->value,
        'delete_themes' => RoleEnum::ADMIN->value,
        'edit_dashboard' => RoleEnum::ADMIN->value,
        'edit_roles' => RoleEnum::ADMIN->value,
        'edit_themes' => RoleEnum::ADMIN->value,
        'groups_access' => RoleEnum::ADMIN->value,
        'groups_admin_groups' => RoleEnum::ADMIN->value,
        'groups_admin_options' => RoleEnum::ADMIN->value,
        'install_plugins' => RoleEnum::ADMIN->value,
        'install_themes' => RoleEnum::ADMIN->value,
        'list_roles' => RoleEnum::ADMIN->value,
        'manage_options' => RoleEnum::ADMIN->value,
        'promote_users' => RoleEnum::ADMIN->value,
        'restrict_content' => RoleEnum::ADMIN->value,
        'ure_create_capabilities' => RoleEnum::ADMIN->value,
        'ure_create_roles' =>RoleEnum::ADMIN->value,
        'ure_delete_capabilities' => RoleEnum::ADMIN->value,
        'ure_delete_roles' => RoleEnum::ADMIN->value,
        'ure_edit_roles' => RoleEnum::ADMIN->value,
        'ure_manage_options' => RoleEnum::ADMIN->value,
        'ure_reset_roles' => RoleEnum::ADMIN->value,
    ];

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
                'source' => 'legacy-data/audio_fader_users_and_permissions.json',
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

        if(!$this->argument('entity'))
        {
        $this->info("Importing users");
        $usersBar = $this->output->createProgressBar(count($rawUsers));
        $usersBar->setFormat('debug');
        $usersBar->start();
        $rawUsers->map(function($rawUser)
            use($rawUserMeta, &$usersBar){
                try{
                    $legacyUserMeta = $rawUserMeta->where('user_id', $rawUser->ID)->pluck('meta_value')->flatten()->toArray();

                    if(count($legacyUserMeta) > 0)
                    {
                        $legacyUserMeta = $legacyUserMeta[0];
                    }else{
                        $legacyUserMeta = null;
                    }

                    $url = $rawUser->user_url === "" ? null : $rawUser->user_url;

                    $user = User::firstOrCreate([
                        'legacy_id' => $rawUser->ID
                    ],[
                        'password' => Hash::make(uniqid()),
                        'email' => $rawUser->user_email,
                        'name' => $rawUser->user_login,
                        'slug' => $rawUser->user_nicename,
                        'created_at' => $rawUser->user_registered,
                    ]);

                    $rawRoles = array_keys(unserialize($rawUser->permissions));
                    $roles = [];

                    foreach($rawRoles as $rawRole)
                    {
                        if(isset($this->oldPermissions[$rawRole]))
                        {
                            $newRole = $this->oldPermissions[$rawRole];
                            $role = Role::firstOrCreate(['name' => $newRole]);
                            $roles[] = $role->name;
                        }

                        if($rawRole === 'bbp_blocked')
                        {
                            $user->blocked_at = now();
                            $user->save();
                            $user = $user->refresh();
                        }

                    }


                    $user->syncRoles($roles);

                    if($url || $legacyUserMeta)
                    {
                        Profile::create([
                            'user_id' => $user->id,
                            'url' => $url,
                            'description' => $legacyUserMeta,
                        ]);
                    }

                }catch(Exception $e)
                {
                    $this->error("Cannot import user {$rawUser->ID}");
                    Log::error("Cannot import user {$rawUser->ID}", ['error' => $e]);
                }
                $usersBar->advance();
        });
        $usersBar->finish();
    }


            $this->info("Importing posts and their meta");
            $bar = $this->output->createProgressBar(count($rawPosts));
            $bar->setFormat('very_verbose');
            $bar->start();
            $rawPosts->map(function($rawPost)
                use($rawCategories, $rawTags, &$rawTaxonomy, &$bar)
                {
                try{
                    DB::transaction(function () use($rawPost, $rawCategories, $rawTags, &$rawTaxonomy){
                        $legacyTaxonomy = $rawTaxonomy->where('object_id', $rawPost->ID);
                        $rawTaxonomy = $rawTaxonomy->forget($legacyTaxonomy->keys());
                        $legacyTaxonomy = $legacyTaxonomy->pluck('term_taxonomy_id')->toArray();
                        $legacyCategories = $rawCategories->whereIn('id', $legacyTaxonomy)->values();
                        $legacyTags = $rawTags->whereIn('id', $legacyTaxonomy)->values();

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

                        $user = User::query()
                            ->where('legacy_id', $rawPost->post_author)
                            ->first();
                            if($rawPost->feature_image_url)
                            {
                                if(Str::contains($rawPost->feature_image_url,'http://localhost/audiofader'))
                                    {
                                        $rawPost->feature_image_url = str_replace('http://localhost/audiofader', 'http://www.audiofader.com', $rawPost->feature_image_url);
                                    }

                            $explodedUrl = explode('/', $rawPost->feature_image_url);
                            $file = end($explodedUrl);
                            $explodedFile = explode('.', $file);
                            $title = $explodedFile[0];
                            $extension = end($explodedFile);
                            $path = "media/{$file}";

                            if (!Storage::disk('public')->exists($path)) {
                                $img = Http::get($rawPost->feature_image_url);
                                Storage::disk('public')->put($path, $img);
                            }

                            $size = Storage::disk('public')->size($path);
                            $mime = Storage::disk('public')->mimeType($path);

                            $imgObj = Media::firstOrCreate([
                                'title' => $title,
                            ], [
                                'disk' => 'public',
                                'visibility' => 'public',
                                'name' => $title,
                                'path' => $path,
                                'ext' => $extension,
                                'title' => $title,
                                'type' => $mime,
                                'size' => $size,
                            ]);
                        }

                        $postStatus = PostStatusEnum::mapLegacy($rawPost->post_status);
                        $publishedDate = $rawPost->post_modified_gmt;

                        if($rawPost->post_status === 'future')
                        {
                            $publishedDate = now()->addMonths(2);
                        }

                        $commentable = 0;

                        if($rawPost->comment_status === 'open')
                        {
                            $commentable = 1;
                        }

                        $post = Post::firstOrCreate([
                            'legacy_id' => $rawPost->ID,
                        ], [
                            'slug' => $rawPost->post_name,
                            'created_at' => Carbon::parse($rawPost->post_date_gmt),
                            'updated_at' => Carbon::parse($rawPost->post_modified_gmt),
                            'status' => $postStatus,
                            'title' => $rawPost->post_title,
                            'content' => $rawPost->post_content,
                            'excerpt' => $rawPost->post_excerpt,
                            'author_id' => $user?->id ?? 1,
                            'published_at' => Carbon::parse($publishedDate),
                            'commentable' => $commentable,
                            'feature_media_id' => isset($imgObj) ? $imgObj->id : null,
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


}
