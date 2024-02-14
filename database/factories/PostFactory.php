<?php

namespace Database\Factories;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(),
            'content' => fake()->randomHtml(),
            'json_content' => [],
            'excerpt' => fake()->text(),
            'status' => 'publish',
            'commentable' => '1',
            'published_at' => Carbon::now(),
            'author_id' => User::factory()->create()->id,
        ];
    }
}
