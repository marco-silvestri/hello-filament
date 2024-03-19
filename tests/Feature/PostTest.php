<?php

namespace Tests\Feature;

use App\Enums\Cms\PostAccessEnum;
use App\Enums\Cms\PostStatusEnum;
use App\Enums\RoleEnum;
use App\Filament\Resources\PostResource\Pages\CreatePost;
use App\Filament\Resources\PostResource\Pages\EditPost;
use App\Filament\Resources\PostResource\Pages\ListPosts;
use App\Models\Post;
use App\Models\Slug;
use App\Models\Tag;
use App\Models\User;
use Carbon\Carbon;
use Filament\Actions\DeleteAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;
use Illuminate\Support\Str;

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
            ->fillForm([
                'title' => $title,
                'author_id' => $author->id,
                'excerpt' => $excerpt,
                'tags' => $tags,
                'json_content' => $this->create_content_for_builder(),
                'published_at' => Carbon::now(),
                'status' => PostStatusEnum::PUBLISH->value,
                'settings.accessible_for' => PostAccessEnum::FREE->value,

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
