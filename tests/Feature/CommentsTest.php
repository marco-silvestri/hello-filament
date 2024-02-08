<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use function PHPUnit\Framework\assertTrue;

class CommentsTest extends TestCase
{
    /** @test */
    public function only_admin_can_access_comments_panel()
    {
        assertTrue(false);
    }

    /** @test */
    public function guest_cannot_insert_comments()
    {
        assertTrue(false);
    }

    /** @test */
    public function user_can_insert_comments()
    {
        assertTrue(false);
    }

    /** @test */
    public function admin_can_insert_comments()
    {
        assertTrue(false);
    }

    /** @test */
    public function guest_cannot_reply_to_comments()
    {
        assertTrue(false);
    }

    /** @test */
    public function user_can_reply_to_comments()
    {
        assertTrue(false);
    }

    /** @test */
    public function admin_can_reply_to_comments()
    {
        assertTrue(false);
    }

    /** @test */
    public function approved_comments_are_shown_below_the_post()
    {
        assertTrue(false);
    }

    /** @test */
    public function awaiting_moderation_comments_arent_shown_below_the_post()
    {
        assertTrue(false);
    }

    /** @test */
    public function rejected_comments_arent_shown_below_the_post()
    {
        assertTrue(false);
    }

    /** @test */
    public function viewable_comments_are_cached()
    {
        assertTrue(false);
    }

    /** @test */
    public function only_admin_can_modify_status_of_comments()
    {
        assertTrue(false);
    }
}
