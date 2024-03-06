<?php

namespace App\Console\Commands\v1;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class UpdatePermission extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-permission';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'update the old permission in the new roles ';

    #definisce i permessi che non sono competenza dell'admin
    private $oldPermissions = [
        'customer' => RoleEnum::CUSTOMER->value,
        'author' => RoleEnum::AUTHOR->value,
        'editor' => RoleEnum::EDITOR->value,
        'contributor' => RoleEnum::EDITOR->value,
        'permesso_modifica_template' => RoleEnum::EDITOR->value,
        'wpseo_editor' => RoleEnum::EDITOR->value,
        'wpseo_manager' => RoleEnum::EDITOR->value,
        'superadmin' => RoleEnum::SUPERADMIN->value,
        'user' => RoleEnum::CUSTOMER->value,
        'subscriber' => RoleEnum::CUSTOMER->value,
    ];
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("starting permission-role conversion");

        try {
            DB::beginTransaction();
            $roles = Role::query()->get();
            $rolesBar = $this->output->createProgressBar($roles->count());
            $rolesBar->setFormat('debug');
            $rolesBar->start();

            foreach ($roles as $role) {
                $this->info('processing ' . $role->name);
                $users = User::role($role->name)->get();
                $userBar = $this->output->createProgressBar(($users->count()));
                $userBar->setFormat('debug');
                $userBar->start();
                #se esiste identifico utenti, aggiungo nuovo ruolo e cancello il veccchio
                if (array_key_exists($role->name, $this->oldPermissions)) {
                    if (!in_array($role->name, RoleEnum::getValues())) {
                        foreach ($users as $user) {
                            $user->removeRole($role->name);
                            $user->assignRole($this->oldPermissions[$role->name]);
                            $userBar->advance();
                        }
                        $role->delete();
                    }
                } else {
                    if ($role->name != RoleEnum::ADMIN->value) {
                        foreach ($users as $user) {
                            $user->removeRole($role->name);
                            $user->assignRole(RoleEnum::ADMIN->value);
                            $userBar->advance();
                        }
                        $role->delete();
                    }
                }
                $rolesBar->advance();
                $userBar->finish();
            }
            $rolesBar->finish();
            DB::commit();
        } catch (\Exception $ex) {
            DB::rollBack();
            $this->error($ex->getMessage());
        }
    }
}
