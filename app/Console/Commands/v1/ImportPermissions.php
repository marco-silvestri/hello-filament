<?php

namespace App\Console\Commands\v1;

use Exception;
use App\Models\Tag;
use App\Models\Post;
use App\Models\User;
use App\Models\Profile;
use App\Models\Category;
use App\Traits\HasWpData;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class ImportPermissions extends Command
{

    use HasWpData;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:permissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import permissions from jsons';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $keysAndSources = [
            'permissions' => [
                'key' => 'wp_capabilities',
                'source' => 'legacy-data/audio_fader_permissions.json',
            ]
        ];

        $rawPermissions = $this->collectWpJson($keysAndSources['permissions']['source'], $keysAndSources['permissions']['key']);

        $bar = $this->output->createProgressBar(count($rawPermissions));
        $bar->setFormat('debug');
        $bar->start();

        $rawPermissions->map(function($rawPermission) use(&$bar){

            $user = User::where('legacy_id', $rawPermission->ID)
                ->first();

            if($user)
            {
                $rawRoles = array_keys(unserialize($rawPermission->permissions));
                $roles = [];

                foreach($rawRoles as $rawRole)
                {
                    $role = Role::firstOrCreate(['name' => $rawRole]);
                    $roles[] = $role->name;
                }

                $user->syncRoles($roles);

            }
            $bar->advance();
        });
        $bar->finish();
        return Command::SUCCESS;
    }
}
