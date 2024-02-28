<?php

namespace App\Policies;

use App\Enums\PermissionsEnum;
use App\Enums\RoleEnum;
use App\Models\Snippet;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SnippetPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo(PermissionsEnum::SNIPPET_VIEW_ALL->value);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Snippet $snippet): bool
    {
        return $user->hasPermissionTo(PermissionsEnum::SNIPPET_VIEW->value);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo(PermissionsEnum::SNIPPET_CREATE->value);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Snippet $snippet): bool
    {
        return $user->hasPermissionTo(PermissionsEnum::SNIPPET_EDIT->value);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Snippet $snippet): bool
    {
        return $user->hasPermissionTo(PermissionsEnum::SNIPPET_DELETE->value);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Snippet $snippet): bool
    {
        return $user->hasPermissionTo(PermissionsEnum::SNIPPET_RESTORE->value);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Snippet $snippet): bool
    {
        return $user->hasRole(RoleEnum::SUPERADMIN->value);
    }
}
