<?php

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Filament\Resources\CategoryResource\Pages\CreateCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Tests\TestCase;
use Illuminate\Support\Str;

class ResourcesTest extends TestCase
{

    use RefreshDatabase;

    /**
     * @test
     */
    public function can_create_category(): void
    {

        $this->seed();
        $user = User::factory()->create();
        $user->assignRole(RoleEnum::SUPERADMIN->value);
        $this->actingAs($user);


        $name = fake()->sentence();
        Livewire::test(CreateCategory::class)
        ->fillForm([
            'name' => $name,
        ])
        ->assertFormSet([
            'slug' => Str::slug($name),
        ]);
    }
}
