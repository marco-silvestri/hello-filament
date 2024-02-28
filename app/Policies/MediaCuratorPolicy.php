<?php

namespace App\Policies;

use App\Enums\PermissionsEnum;
use App\Enums\RoleEnum;
use App\Models\User;
use Awcodes\Curator\Models\Media;

class MediaCuratorPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo(PermissionsEnum::MEDIA_VIEW_ALL->value);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Media $media): bool
    {
        return $user->hasPermissionTo(PermissionsEnum::MEDIA_VIEW->value);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo(PermissionsEnum::MEDIA_CREATE->value);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Media $media): bool
    {
        return $user->hasPermissionTo(PermissionsEnum::MEDIA_EDIT->value);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Media $media): bool
    {
        return $user->hasPermissionTo(PermissionsEnum::MEDIA_DELETE->value);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Media $media): bool
    {
        return $user->hasPermissionTo(PermissionsEnum::MEDIA_RESTORE->value);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Media $media): bool
    {
        return $user->hasRole(RoleEnum::SUPERADMIN->value);
    }
}
