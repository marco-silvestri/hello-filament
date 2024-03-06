<?php

namespace Tests\Feature;

use App\Enums\Cms\MenuOptionsEnum;
use App\Enums\RoleEnum;
use App\Filament\Resources\MenuResource;
use App\Filament\Resources\MenuResource\Pages\CreateMenu;
use App\Filament\Resources\MenuResource\Pages\EditMenu;
use App\Models\Menu;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Tests\TestCase;

class MenuTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function can_create_menu(): void
    {

        $this->seed();
        $user = User::factory()->create();
        $user->assignRole(RoleEnum::SUPERADMIN->value);
        $this->actingAs($user);


        $name = fake()->sentence();
        Livewire::test(CreateMenu::class)
            ->fillForm([
                'name' => $name,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas(Menu::class, [
            'name' => $name,
        ]);
    }

    /**
     * @test
     */
    public function can_update_menu(): void
    {
        $this->seed();
        $user = User::factory()->create();
        $user->assignRole(RoleEnum::SUPERADMIN->value);
        $this->actingAs($user);

        $menu = Menu::factory()->create();

        Livewire::test(EditMenu::class, [
            'record' => $menu->getRouteKey(),
        ])->assertFormSet([
            'name' => $menu->name
        ])->fillForm([
            'name' => fake()->sentence(),
        ])
            ->call('save')
            ->assertHasNoFormErrors();
    }

    /**
     * @test
     */
    public function can_delete_menu(): void
    {
        $this->seed();
        $user = User::factory()->create();
        $user->assignRole(RoleEnum::SUPERADMIN->value);
        $this->actingAs($user);

        $menu = Menu::factory()->create();

        Livewire::test(EditMenu::class, [
            'record' => $menu->getRouteKey(),
        ])->callAction(DeleteAction::class);

        $this->assertDatabaseMissing(Menu::class, [
            'name' => $menu->name,
        ]);
    }

    /**
     * @test
     */
    // public function can_create_menu_item()
    // {
    //     $this->seed();
    //     $user = User::factory()->create();
    //     $user->assignRole(RoleEnum::SUPERADMIN->value);
    //     $this->actingAs($user);

    //     $menu = Menu::factory()->create();

    //     Livewire::test(MenuResource::class, [
    //         'record' => $menu->getRouteKey(),
    //     ])->callAction('add_menu_item')
    //         ->fillForm([
    //             'name' => fake()->word(),
    //             'parent_id' => null,
    //             'order' => fake()->numberBetween(1, 5),
    //             'has_submenu' => fake()->boolean(),
    //             'type' => fake()->randomElement(MenuOptionsEnum::cases()),
    //             'value' => fake()->numberBetween(1, 5),
    //             'menu_id' => null
    //         ])
    //         ->assertHasNoActionErrors();
    // }
}
