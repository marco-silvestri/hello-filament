<?php

namespace App\Models;

use Filament\Panel;
use App\Models\Post;
use App\Enums\RoleEnum;
use App\Models\Profile;
use App\Traits\Cms\HasSlug;
use Spatie\Permission\Traits\HasRoles;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, HasRoles, SoftDeletes, HasSlug;

    protected $guarded = ['id'];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->hasAnyRole([
            RoleEnum::SUPERADMIN,
            RoleEnum::ADMIN,
            RoleEnum::AUTHOR,
            RoleEnum::EDITOR,
        ]);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class, 'author_id', 'id');
    }

    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class);
    }

    public function searches():HasMany{
        return $this->hasMany(User::class);
    }
}
