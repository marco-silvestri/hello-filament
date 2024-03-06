<?php

namespace Database\Factories;

use App\Enums\Cms\MenuOptionsEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MenuItem>
 */
class MenuItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->word(),
            'parent_id' => null,
            'order' => fake()->numberBetween(1,5),
            'has_submenu' => fake()->boolean(),
            'type' => fake()->randomElement(MenuOptionsEnum::cases()->toArray())2,
            'value' => fake()->numberBetween(1,5),
            'menu_id' => null
        ];
    }
}
