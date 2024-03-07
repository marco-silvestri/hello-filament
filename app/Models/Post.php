<?php

namespace App\Models;

use App\Traits\Cms\HasSlug;
use App\Traits\Cms\HasVisits;
use Awcodes\Curator\Models\Media;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory, HasVisits, SoftDeletes, HasSlug;

    protected $guarded = ['id'];

    protected $casts = [
        'json_content' => 'array',
        'published_at' => 'datetime:Y-m-d H:i:s'
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id', 'id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function slug(): MorphOne
    {
        return $this->morphOne(Slug::class, 'sluggable');
    }

    public function audio(): BelongsToMany
    {
        return $this->belongsToMany(Audio::class);
    }

    public function featuredImage(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'feature_media_id');
    }
}
