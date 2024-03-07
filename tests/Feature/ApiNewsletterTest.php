<?php

namespace Tests\Feature;

use App\Enums\Cms\InternalNewsletterStatusEnum;
use Tests\TestCase;
use App\Models\Post;
use App\Models\Newsletter;
use Illuminate\Support\Facades\Cache;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ApiNewsletterTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function newsletter_token_endpoint_responds_with_token_if_auth_is_correct()
    {
        $response = $this->post('/api/newsletter/token', [
            'utente' => config('cms.internal_newsletter_api.user'),
            'password' => config('cms.internal_newsletter_api.user'),
        ]);

        $response->assertJsonStructure([
            'http_code',
            'message',
            'data' => [
                'token'
            ]
        ]);

        $token = Cache::get('internal_newsletter_token');

        $this->assertEquals($token, $response['data']['token']);
    }

    /** @test */
    public function newsletter_token_endpoint_responds_with_401_if_auth_isnt_correct()
    {
        $response = $this->post('/api/newsletter/token', [
            'utente' => fake()->word(),
            'password' => fake()->word(),
        ]);

        $response->assertJsonStructure([
            'message',
        ]);

        $response->assertUnauthorized();
    }

    /** @test */
    public function newsletter_get_endpoint_responds_with_newsletters_if_token_is_valid()
    {
        $token = 'mocked_token';
        Cache::put('internal_newsletter_token', $token, 60);

        $post = Post::factory()->create();

        Newsletter::factory()->create([
            'status' => InternalNewsletterStatusEnum::SENT,
            'json_content' => [
                [
                    "data" =>
                    [
                        "posts" => $post->id,
                        "title" => $post->title,
                        "excerpt" => $post->excerpt,
                        "alignment" => "left",
                        "featureImage" => 1,
                        "published_at" => "2024-05-01 15:16:07"
                    ],
                    "type" => "related_posts"
                ],
        ]]);

        $response = $this->post('/api/newsletter/get', [
            'auth_token' => $token,
            'mailing_id' => 2,
        ]);

        $response->assertOk();

        $response->assertJsonStructure($this->getNewsletterJsonStructure(), $response->json());
    }

    /** @test */
    public function newsletters_get_endpoint_responds_with_401_if_token_isnt_valid()
    {
        $response = $this->post('/api/newsletter/get', [
            'auth_token' => 'I am not a token',
            'mailing_id' => 2,
        ]);

        $response->assertUnauthorized();
    }

    /** @test */
    public function update_endpoint_responds_with_200_if_token_is_valid()
    {
        $token = 'mocked_token';
        Cache::put('internal_newsletter_token', $token, 60);

        $post = Post::factory()->create();

        $newsletter = Newsletter::factory()->create([
            'status' => InternalNewsletterStatusEnum::SENT,
            'json_content' => [
                [
                    "data" =>
                    [
                        "posts" => $post->id,
                        "title" => $post->title,
                        "excerpt" => $post->excerpt,
                        "alignment" => "left",
                        "featureImage" => 1,
                        "published_at" => "2024-05-01 15:16:07"
                    ],
                    "type" => "related_posts"
                ],
        ]]);

        $response = $this->post('/api/newsletter/update', [
            'auth_token' => $token,
            'id_newsletter' => $newsletter->id,
            'stato' => 2,
        ]);

        $response->assertOk();

        $this->assertDatabaseHas('newsletters', [
            'id' => $newsletter->id,
            'status' => InternalNewsletterStatusEnum::LOADING,
        ]);

        $this->assertDatabaseMissing('newsletters', [
            'id' => $newsletter->id,
            'status' => InternalNewsletterStatusEnum::SENT,
        ]);
    }

    /** @test */
    public function update_endpoint_responds_with_500_if_token_isnt_valid()
    {
        $response = $this->post('/api/newsletter/update', [
            'auth_token' => 'I am not a token',
            'id_newsletter' => 2,
            'stato' => 4,
        ]);

        $response->assertInternalServerError();
    }

    /** @test */
    public function preview_endpoint_responds_with_payload_if_token_is_valid()
    {
        $token = 'mocked_token';
        Cache::put('internal_newsletter_token', $token, 60);

        $post = Post::factory()->create();

        $newsletter = Newsletter::factory()->create([
            'status' => InternalNewsletterStatusEnum::SENT,
            'json_content' => [
                [
                    "data" =>
                    [
                        "posts" => $post->id,
                        "title" => $post->title,
                        "excerpt" => $post->excerpt,
                        "alignment" => "left",
                        "featureImage" => 1,
                        "published_at" => "2024-05-01 15:16:07"
                    ],
                    "type" => "related_posts"
                ],
        ]]);

        $response = $this->post('/api/newsletter/preview', [
            'auth_token' => $token,
            'id_newsletter' => $newsletter->id,
        ]);

        $response->assertOk();

        $response->assertJsonStructure($this->getNewsletterJsonStructure(), $response->json());

    }

    /** @test */
    public function preview_endpoint_responds_with_401_if_token_isnt_valid()
    {
        $response = $this->post('/api/newsletter/preview', [
            'auth_token' => 'I am not a token',
            'id_newsletter' => 1,
        ]);

        $response->assertUnauthorized();
    }

    /** @test */
    public function preview_endpoint_responds_with_404_if_newsletter_doesnt_exist()
    {
        $token = 'mocked_token';
        Cache::put('internal_newsletter_token', $token, 60);

        $response = $this->post('/api/newsletter/preview', [
            'auth_token' => $token,
            'id_newsletter' => 1,
        ]);

        $response->assertNotFound();
    }

    private function getNewsletterJsonStructure():array
    {
        return [
            '*' => [
                'config' => [
                    '*' => [
                        'alias',
                        'mail',
                        'reply',
                        'preview',
                        'alert',
                        'id_newsletter',
                        'nome_campagna',
                        'oggetto',
                        'preheader',
                        'data_invio',
                        'nr_newsletter',
                        'mailing_id',
                    ],
                ],
                'body' => [
                    '*' => [
                        '*' => [
                            'tipo',
                            'data' => [
                                'posizione_articolo',
                                'titolo',
                                'autologin_mk',
                                'sottotitolo',
                                'titolo_sponsor',
                                'testo_azienda',
                                'data_articolo',
                                'testo_abstract',
                                'link_immagine',
                                'link_articolo',
                                'nome_tag',
                                'link_tag',
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
