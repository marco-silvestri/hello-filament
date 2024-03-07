<?php

namespace Database\Factories;

use App\Enums\Cms\InternalNewsletterStatusEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Newsletter>
 */
class NewsletterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(5,true),
            'subject' => fake()->words(3,true),
            'pre_header' => null,
            'send_date' => Carbon::now()->subDay(),
            'number' => 1,
            'type' => 2,
            'json_content' => [],
            'status' => InternalNewsletterStatusEnum::DRAFT,
        ];
    }
}
