<?php

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Filament\Resources\TagResource\Pages\CreateTag;
use App\Filament\Resources\TagResource\Pages\EditTag;
use App\Filament\Resources\TagResource\Pages\ListTags;
use App\Models\Slug;
use App\Models\Tag;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Tests\TestCase;

class TagTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function can_create_tag(): void
    {

        $this->seed();
        $user = User::factory()->create();
        $user->assignRole(RoleEnum::SUPERADMIN->value);
        $this->actingAs($user);


        $name = fake()->sentence();
        Livewire::test(CreateTag::class)
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
        $this->assertDatabaseHas(Tag::class, [
            'name' => $name,
        ]);
    }

    /**
     * @test
     */
    public function can_list_tag(): void
    {
        $this->seed();
        $user = User::factory()->create();
        $user->assignRole(RoleEnum::SUPERADMIN->value);
        $this->actingAs($user);
        $records = Tag::factory(3)->hasSlug()->create();
        Livewire::test(ListTags::class)
            ->assertCanSeeTableRecords($records);
    }


    /**
     * @test
     */
    public function can_update_tag(): void
    {
        $this->seed();
        $user = User::factory()->create();
        $user->assignRole(RoleEnum::SUPERADMIN->value);
        $this->actingAs($user);

        $record = Tag::factory()->hasSlug()->create();

        Livewire::test(EditTag::class, [
            'record' => $record->getRouteKey(),
        ])->assertFormSet([
            'name' => $record->name
        ])->fillForm([
            'name' => fake()->sentence(),
        ])
            ->call('save')
            ->assertHasNoFormErrors();
    }

    /**
     * @test
     */
    public function can_delete_tag(): void
    {
        $this->seed();
        $user = User::factory()->create();
        $user->assignRole(RoleEnum::SUPERADMIN->value);
        $this->actingAs($user);

        $record = Tag::factory()->create();

        Livewire::test(EditTag::class, [
            'record' => $record->getRouteKey(),
        ])->callAction(DeleteAction::class);

        $record->refresh();

        $this->assertDatabaseHas(Tag::class, [
            'name' => $record->name,
            'deleted_at' => $record->deleted_at
        ]);
    }
}
