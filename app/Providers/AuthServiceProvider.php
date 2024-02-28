<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Policies\MediaCuratorPolicy;
use App\Policies\RoleSpatiePolicy;
use Awcodes\Curator\Models\Media;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Spatie\Permission\Models\Role;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Media::class => MediaCuratorPolicy::class,
        Role::class => RoleSpatiePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
