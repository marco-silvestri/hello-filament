<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Post;
use App\Models\User;
use Livewire\Livewire;
use App\Enums\RoleEnum;

use App\Livewire\Cms\Comment;
use App\Enums\Cms\CommentStatusEnum;

use Illuminate\Support\Facades\Event;
use function PHPUnit\Framework\assertTrue;
use Spatie\Permission\PermissionRegistrar;
use App\Filament\Resources\CommentResource;
use Database\Seeders\RolesPermissionsSeeder;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Database\Events\DatabaseRefreshed;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CommentsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp():void
    {
        parent::setUp();
        config()->set('honeypot.enabled', false);
        app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }
    /** @test */
    public function guest_cannot_access_comments_panel()
    {
        $response = $this->get('admin/comments');
        $response->assertRedirect();
    }

    /** @test */
    public function guest_can_insert_comments()
    {
        $post = Post::factory()->create();

        Livewire::test(Comment::class, ['postId'=> $post->id])
            ->set('newComment', 'test comment')
            ->call('sendComment');

        $this->assertDatabaseHas('comments', [
            'body' => 'test comment',
            'status' => CommentStatusEnum::AWAITING_MODERATION,
            'author_id' => null,
            'post_id' => $post->id,
        ]);
    }

    /** @test */
    public function user_can_insert_comments()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();
        $this->actingAs($user);
        Livewire::test(Comment::class, ['postId'=> $post->id])
            ->set('newComment', 'test comment')
            ->call('sendComment');

        $this->assertDatabaseHas('comments', [
            'body' => 'test comment',
            'status' => CommentStatusEnum::AWAITING_MODERATION,
            'author_id' => $user->id,
            'post_id' => $post->id,
        ]);
    }
}
