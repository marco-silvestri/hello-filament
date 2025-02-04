<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Traits\Tappable;

class HomePageSetting extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function groupable(): MorphTo
    {
        return $this->morphTo();
    }

    public function getPosts(int $limit = 3): Collection
    {
        return $this->groupable->posts()
            ->published()
            ->with(['featuredImage', 'slug'])
            ->when($limit > 0, fn($q) => $q->limit($limit))
            ->select('posts.id', 'feature_media_id', 'title', 'published_at')->get();
    }
}
