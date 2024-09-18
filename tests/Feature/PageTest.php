<?php

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Filament\Resources\PageResource\Pages\CreatePage;
use App\Filament\Resources\PageResource\Pages\EditPage;
use App\Filament\Resources\PageResource\Pages\ListPages;
use App\Models\Page;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Tests\TestCase;
use Illuminate\Support\Str;


class PageTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function can_create_page(): void
    {

        $this->seed();
        $user = User::factory()->create();
        $user->assignRole(RoleEnum::SUPERADMIN->value);
        $this->actingAs($user);

        $name = fake()->sentence();
        Livewire::test(CreatePage::class)
            ->fillForm([
                'title' => $name,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas(Page::class, [
            'title' => $name,
        ]);
    }

    /**
     * @test
     */
    public function can_list_page(): void
    {
        $this->seed();
        $user = User::factory()->create();
        $user->assignRole(RoleEnum::SUPERADMIN->value);
        $this->actingAs($user);
        $records = Page::factory(3)->create();
        Livewire::test(ListPages::class)
            ->assertCanSeeTableRecords($records);
    }


    /**
     * @test
     */
    public function can_update_page(): void
    {
        $this->seed();
        $user = User::factory()->create();
        $user->assignRole(RoleEnum::SUPERADMIN->value);
        $this->actingAs($user);

        $record = Page::factory()->hasSlug()->create();

        Livewire::test(EditPage::class, [
            'record' => $record->getRouteKey(),
        ])->assertFormSet([
            'title' => $record->title
        ])->fillForm([
            'title' => fake()->sentence(),
        ])
            ->call('save')
            ->assertHasNoFormErrors();
    }

    /**
     * @test
     */
    public function can_delete_page(): void
    {
        $this->seed();
        $user = User::factory()->create();
        $user->assignRole(RoleEnum::SUPERADMIN->value);
        $this->actingAs($user);

        $record = Page::factory()->hasSlug()->create();

        Livewire::test(EditPage::class, [
            'record' => $record->getRouteKey(),
        ])->callAction(DeleteAction::class);

        $record->refresh();

        $this->assertDatabaseHas(Page::class, [
            'title' => $record->title,
            'deleted_at' => $record->deleted_at
        ]);
    }

    /**
     * @test
     */
    public function can_access_page_via_slug()
    {
        $record = Page::factory()->hasSlug()->create();

        $res = $this->get("/pagina/{$record->slug->name}");

        $res->assertOk()->assertSee($record->title);
    }

    /**
     * @test
     */
    public function if_page_doesnt_exist_returns_404()
    {
        $res = $this->get("/page/non-existing-slug");

        $res->assertNotFound();
    }
}
