<?php

namespace Tests\Feature;

use Carbon\Carbon;
use App\Models\Tag;
use Tests\TestCase;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Slug;
use App\Models\User;
use Livewire\Livewire;
use App\Enums\RoleEnum;
use Illuminate\Support\Str;
use App\Enums\Cms\PostAccessEnum;
use App\Enums\Cms\PostStatusEnum;
use App\Models\Cms\PostPlannings;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Repeater;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Filament\Resources\PostResource\Pages\EditPost;
use App\Filament\Resources\PostResource\Pages\ListPosts;
use App\Filament\Resources\PostResource\Pages\CreatePost;
use Filament\Facades\Filament;

class PostTest extends TestCase
{

    use RefreshDatabase;

    /**
     * @test
     */
    public function can_create_post(): void
    {
        $this->seed();
        $user = User::factory()->create();
        $user->assignRole(RoleEnum::SUPERADMIN->value);
        $this->actingAs($user);

        $author = User::factory()->create();
        $author->assignRole(RoleEnum::AUTHOR->value);

        $title = fake()->sentence();
        $excerpt = fake()->text();
        $tags = Tag::factory(2)->hasSlug()->create();

        Livewire::test(CreatePost::class)
            ->set('data.plannings', null)
            ->fillForm([
                'title' => $title,
                'author_id' => $author->id,
                'excerpt' => $excerpt,
                'tags' => $tags,
                'json_content' => $this->create_content_for_builder(),
                'published_at' => Carbon::now(),
                'status' => PostStatusEnum::PUBLISH->value,
                'settings.accessible_for' => PostAccessEnum::FREE->value

            ])
            ->assertFormSet([
                'slug.name' => Str::slug($title),
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas(Slug::class, [
            'name' => Str::slug($title),
        ]);
        $this->assertDatabaseHas(Post::class, [
            'title' => $title,
            'author_id' => $author->id,
        ]);
    }



    /**
     * @test
     */
    public function can_list_post(): void
    {
        $this->seed();
        $user = User::factory()->create();
        $user->assignRole(RoleEnum::SUPERADMIN->value);
        $this->actingAs($user);
        $records = Post::factory(3)->hasSlug()->hasAuthor()->create();
        Livewire::test(ListPosts::class)
            ->assertCanSeeTableRecords($records);
    }

    /**
     * @test
     */
    public function can_update_post(): void
    {
        $this->seed();
        $user = User::factory()->create();
        $user->assignRole(RoleEnum::SUPERADMIN->value);
        $this->actingAs($user);

        $newAuthor = User::factory()->create();
        $newAuthor->assignRole(RoleEnum::AUTHOR->value);

        $record = Post::factory()->hasSlug()->hasAuthor()->create();

        Livewire::test(EditPost::class, [
            'record' => $record->getRouteKey(),
        ])->assertFormSet([
            'title' => $record->title
        ])->fillForm([
            'title' => fake()->sentence(),
            'author_id' => $newAuthor->id,
            'settings.accessible_for' => PostAccessEnum::FREE->value,
        ])
            ->call('save')
            ->assertHasNoFormErrors();
    }

    /**
     * @test
     */
    public function can_delete_post(): void
    {
        $this->seed();
        $user = User::factory()->create();
        $user->assignRole(RoleEnum::SUPERADMIN->value);
        $this->actingAs($user);

        $record = Post::factory()->hasSlug()->hasAuthor()->create();

        Livewire::test(EditPost::class, [
            'record' => $record->getRouteKey(),
        ])->callAction(DeleteAction::class);

        $record->refresh();

        $this->assertDatabaseHas(Post::class, [
            'title' => $record->title,
            'deleted_at' => $record->deleted_at
        ]);
    }

    /**
     * @test
     */
    public function scope_published_returns_scoped_posts()
    {
        $this->seed();

        $records = Post::factory(5)->hasSlug()->hasAuthor()->create();

        Post::factory(2)
            ->hasSlug()->hasAuthor()
            ->hasPlannings()
            ->create([
                'status' => PostStatusEnum::PLANNED
            ]);

        $this->assertCount(5, Post::published()->get());
        $this->assertCount(7, Post::get());
    }

    /**
     * @test
     */
    public function preview_return_login_if_user_is_not_admin()
    {
        $this->seed();

        $res = $this->get("/admin/preview/999");
        $res->assertRedirectToRoute('filament.admin.auth.login');
    }

    /**
     * @test
     */
    public function preview_return_view_with_post_for_admin()
    {
        $this->seed();
        $admin = User::factory()->create();
        $admin->assignRole(RoleEnum::SUPERADMIN->value);

        $post = Post::factory()
            ->hasSlug()
            ->hasAuthor()
            ->create();

        $this->actingAs($admin);
        $view = $this->view('cms.blog.post', [
            'menu' => [],
            'post' => $post,
            'isPreview' => true,
        ]);

        $view->assertSee(__('posts.lbl-preview'))
            ->assertDontSee(__('comments.lbl-comments'));
    }

    /**
     * @test
    */

    public function can_see_post_comments()
    {
        // Set config value to true
        config('app.comments', true);
        $this->seed();
        $admin = User::factory()->create();
        $admin->assignRole(RoleEnum::SUPERADMIN->value);

        $post = Post::factory()
            ->hasSlug()
            ->hasAuthor()
            ->create();

        // Manually create comments and associate them with the post
        for ($i = 0; $i < 3; $i++) {
            $comment = new Comment([
                'post_id' => $post->id,
                'body' => 'Sample comment content',
            ]);
            $comment->save();
        }

        $post->commentsCount = count($post->comments);

        $this->actingAs($admin);
        $view = $this->view('cms.blog.post', [
            'menu' => [],
            'post' => $post,
        ]);

        $view->assertSee($post->commentsCount);
        $view->assertSee(__('comments.lbl-comments'));
    }

    /**
     * @test
    */

    public function can_not_see_post_comments()
    {
        // Set config value to false
        config('app.comments', false);

        $this->seed();
        $admin = User::factory()->create();
        $admin->assignRole(RoleEnum::SUPERADMIN->value);

        $post = Post::factory()
        ->hasSlug()
        ->hasAuthor()
        ->create();

        // Manually create comments and associate them with the post
        for ($i = 0; $i < 3; $i++) {
            $comment = new Comment([
                'post_id' => $post->id,
                'body' => 'Sample comment content',
            ]);
            $comment->save();
        }

        $this->actingAs($admin);
        $view = $this->view('cms.blog.post', [
            'menu' => [],
            'post' => $post,
        ]);

        $view->assertDontSee(__('comments.lbl-comments'));
    }



    private function create_content_for_builder()
    {
        $blocks = [
            [
                "type" => 'heading',
                "data" => [
                    'color' => fake()->hexColor(),
                    'level' => 'h1',
                    'content' => fake()->sentence()
                ],
            ],
            [
                "type" => 'paragraph',
                "data" => [
                    'content' => fake()->text()
                ],
            ],
        ];


        return $blocks;
    }
}
