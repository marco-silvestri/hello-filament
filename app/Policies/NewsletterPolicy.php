<?php

namespace App\Policies;

use App\Enums\PermissionsEnum;
use App\Enums\RoleEnum;
use App\Models\Newsletter;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class NewsletterPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo(PermissionsEnum::NEWSLETTER_VIEW_ALL->value);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Newsletter $newsletter): bool
    {
        return $user->hasPermissionTo(PermissionsEnum::NEWSLETTER_VIEW->value);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo(PermissionsEnum::NEWSLETTER_CREATE->value);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Newsletter $newsletter): bool
    {
        if ($user->hasPermissionTo(PermissionsEnum::NEWSLETTER_EDIT->value) && $newsletter->status->value == 'draft' ) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Newsletter $newsletter): bool
    {
        return $user->hasPermissionTo(PermissionsEnum::NEWSLETTER_DELETE->value);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Newsletter $newsletter): bool
    {
        return $user->hasPermissionTo(PermissionsEnum::NEWSLETTER_RESTORE->value);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Newsletter $newsletter): bool
    {
        return $user->hasRole(RoleEnum::SUPERADMIN->value);
    }
}
