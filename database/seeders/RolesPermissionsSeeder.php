<?php

namespace Database\Seeders;

use App\Enums\PermissionsEnum;
use App\Enums\RoleEnum;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (RoleEnum::cases() as $case) {
            Role::findOrCreate($case->value);
        }

        foreach (PermissionsEnum::cases() as $case) {
            $permission = Permission::firstOrCreate(['name' => $case->value]);
            $permission->syncRoles($case->roles());
        }

    }
}
