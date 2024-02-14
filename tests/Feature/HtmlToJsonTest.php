<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Post;
use App\Models\Slug;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class HtmlToJsonTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function parser_works_correctly()
    {
        Storage::fake();
        $author = User::factory()->create();

        $this->mockHttpResponses();

        $referencedPost = Post::factory()->hasSlug(1, [
            'name' => 'article-1',
        ])->create([
            'content' => fake()->randomHtml(),
        ]);

        $post = Post::factory()->create([
            'content' => $this->getHtmlBody(),
            'author_id' => $author->id,
        ]);

        $this->artisan('app:html-to-json');

        $post = $post->refresh();

        $this->assertTrue($post->has_importer_problem === 0);
    }

    public function mockHttpResponses()
    {
        Http::fake([
            'audiofader.com/images/*' => Http::response('images', 200),
            'audiofader.com/audio/*' => Http::response('audio', 200),
            'audiofader.com/*' => Http::response('main', 200),
        ]);
    }

    public function getHtmlBody()
    {
        return <<<HTML
        <h2 style="text-align: center;"><span style="color: #008080;">Lorem ipsum dolor sit, amet consectetur adipisicing elit. Libero, assumenda quisquam. Ullam quod quos commodi beatae fugiat consectetur odio eveniet voluptates nesciunt tempore, quisquam esse eos error, obcaecati ipsam ipsum!</span></h2>

        <strong>Lorem ipsum dolor, sit amet consectetur</strong> adipisicing elit. Eum, sunt doloremque nihil vel non dolore consequatur, expedita illo corrupti eligendi, incidunt ducimus sequi saepe hic.

        <a href="https://www.audiofader.com/images/fakeimage.png" target="_blank" rel="noopener"><img src="https://www.audiofader.com/images/fakeimage.png" alt="alternative tags" width="553" height="450" /></a>

        [audio mp3="https://www.audiofader.com/audio/fakeaudio.mp3"][/audio]

        <p>[caption]<a href="https://www.audiofader.com/images/fakeimage.png" target="_blank" rel="noopener"><img src="https://www.audiofader.com/images/fakeimage.png" alt="alternative tags" width="779" height="423" /></a><em>I'm a caption</em>[/caption]</p>

        [embed]https://youtu.be/JFK9erJ4t9Y[/embed]

        &nbsp;

        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Repudiandae, illo possimus! Voluptatem quos earum illo, quas modi quod veniam. Officia quidem accusamus cumque incidunt quasi, perspiciatis natus sequi ab commodi.</p>

        https://www.audiofader.com/article-1/
        HTML;
    }
}
