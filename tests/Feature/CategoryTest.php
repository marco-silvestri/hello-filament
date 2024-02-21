<?php

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Filament\Resources\CategoryResource\Pages\CreateCategory;
use App\Filament\Resources\CategoryResource\Pages\EditCategory;
use App\Filament\Resources\CategoryResource\Pages\ListCategories;
use App\Models\Category;
use App\Models\Slug;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;
use Illuminate\Support\Str;

class CategoryTest extends TestCase
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
                'slug.name' => Str::slug($name),
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas(Slug::class, [
            'name' => Str::slug($name),
        ]);
        $this->assertDatabaseHas(Category::class, [
            'name' => $name,
        ]);
    }

    /**
     * @test
     */
    public function can_list_category(): void
    {
        $this->seed();
        $user = User::factory()->create();
        $user->assignRole(RoleEnum::SUPERADMIN->value);
        $this->actingAs($user);
        $categories = Category::factory(3)->hasSlug()->create();
        Livewire::test(ListCategories::class)
            ->assertCanSeeTableRecords($categories);
    }


    /**
     * @test
     */
    public function can_update_category(): void
    {
        $this->seed();
        $user = User::factory()->create();
        $user->assignRole(RoleEnum::SUPERADMIN->value);
        $this->actingAs($user);

        $category = Category::factory()->create();

        Livewire::test(EditCategory::class, [
            'record' => $category->getRouteKey(),
        ])->assertFormSet([
            'name' => $category->name
        ])->fillForm([
            'name' => fake()->sentence(),
        ])
            ->call('save')
            ->assertHasNoFormErrors();
    }

    /**
     * @test
     */
    public function can_delete_category(): void
    {
        $this->seed();
        $user = User::factory()->create();
        $user->assignRole(RoleEnum::SUPERADMIN->value);
        $this->actingAs($user);

        $category = Category::factory()->create();

        Livewire::test(EditCategory::class, [
            'record' => $category->getRouteKey(),
        ])->callAction(DeleteAction::class);

        $category->refresh();

        $this->assertDatabaseHas(Category::class, [
            'name' => $category->name,
            'deleted_at' => $category->deleted_at
        ]);
    }
}
