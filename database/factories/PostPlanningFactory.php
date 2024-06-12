<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class PostPlanningFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'start_at' => Carbon::today()->subDays(rand(100, 180))->format('Y-m-d'),
            'end_at' => Carbon::today()->subDays(rand(0, 100))->format('Y-m-d')
        ];
    }
}
