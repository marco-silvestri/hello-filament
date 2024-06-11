<?php

namespace App\Models;

use App\Models\Post;
use App\Traits\Cms\HasGroup;
use App\Traits\Cms\HasSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    use HasFactory, SoftDeletes, HasSlug, HasGroup;

    protected $guarded = ['id'];

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class);
    }
}
