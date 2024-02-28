<?php

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Models\User;
use Awcodes\Curator\Models\Media;
use Awcodes\Curator\Resources\MediaResource\CreateMedia;
use Awcodes\Curator\Resources\MediaResource\EditMedia;
use Awcodes\Curator\Resources\MediaResource\ListMedia;
use Filament\Actions\DeleteAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class MediaTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function can_create_media(): void
    {

        Storage::fake('public');
        $this->seed();
        $user = User::factory()->create();
        $user->assignRole(RoleEnum::SUPERADMIN->value);
        $this->actingAs($user);
        $file = UploadedFile::fake()->image('avatar.png');

        Livewire::test(CreateMedia::class)
            ->fillForm([
                'file' => $file,
            ])
            ->call('create')
            ->assertHasNoFormErrors();
        $filePath = Storage::disk('public')->files('media')[0];

        $this->assertDatabaseHas(Media::class, [
            'path' => $filePath,
        ]);
    }

    /**
     * @test
     */
    public function can_list_media(): void
    {
        $this->seed();
        $user = User::factory()->create();
        $user->assignRole(RoleEnum::SUPERADMIN->value);
        $this->actingAs($user);
        $records = Media::factory(3)->create();
        Livewire::test(ListMedia::class)
            ->assertCanSeeTableRecords($records);
    }


    /**
     * @test
     */
    public function can_update_media(): void
    {
        $this->seed();
        $user = User::factory()->create();
        $user->assignRole(RoleEnum::SUPERADMIN->value);
        $this->actingAs($user);

        $record = Media::factory()->create();

        $title = fake()->sentence();
        Livewire::test(EditMedia::class, [
            'record' => $record->getRouteKey(),
        ])->fillForm([
            'alt' => fake()->sentence(),
            'title' => $title
        ])
            ->call('save')
            ->assertHasNoFormErrors();


        $this->assertDatabaseHas(Media::class, [
            'title' => $title,
        ]);
    }

    /**
     * @test
     */
    public function can_delete_media(): void
    {
        $this->seed();
        $user = User::factory()->create();
        $user->assignRole(RoleEnum::SUPERADMIN->value);
        $this->actingAs($user);

        $record = Media::factory()->create();

        Livewire::test(EditMedia::class, [
            'record' => $record->getRouteKey(),
        ])->callAction(DeleteAction::class);


        $this->assertModelMissing($record);
    }
}
