<?php

namespace Database\Factories;

use App\Models\Cms\PostPlannings;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class PostPlanningsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
protected $model = PostPlannings::class;

    public function definition(): array
    {
        return [
            'start_at' => Carbon::today()->subDays(rand(0, 180))->format('Y-m-d'),
            'end_at' => Carbon::today()->subDays(rand(0, 180))->format('Y-m-d')
        ];
    }
}
