<?php

namespace App\Models;

use App\Enums\RoleEnum;
use Filament\Panel;
use App\Models\Post;
use App\Models\Profile;
use App\Traits\Cms\HasSlug;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
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
}
