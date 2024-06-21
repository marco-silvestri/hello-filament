<?php

namespace App\Models;

use App\Traits\Cms\HasSlug;
use App\Traits\Cms\HasAuthor;
use App\Traits\Cms\HasVisits;
use App\Models\Cms\PostSettings;
use App\Enums\Cms\PostStatusEnum;
use App\Models\Cms\PostPlannings;
use Awcodes\Curator\Models\Media;
use Illuminate\Support\Collection;
use App\Traits\Cms\HasStringOperations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Post extends Model
{
    use HasFactory, HasVisits, SoftDeletes, HasSlug, HasAuthor, HasStringOperations;

    protected $guarded = ['id'];

    protected $casts = [
        'json_content' => 'array',
        'published_at' => 'datetime:Y-m-d H:i:s'
    ];

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function audio(): BelongsToMany
    {
        return $this->belongsToMany(Audio::class);
    }

    public function featuredImage(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'feature_media_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function scheduled(): HasMany
    {
        return $this->hasMany(ScheduledPost::class);
    }

    public function relatedPosts()
    {
        return $this->belongsToMany(Post::class, 'related_posts', 'post_id', 'related_post_id');
    }

    public function scopePublished($query)
    {
        return $query
            ->where('status', PostStatusEnum::PUBLISH)
            ->where('published_at', '<=', now())
            ->whereNotNull('published_at')
            ->where(function(Builder $post) {
                $post->doesntHave('plannings')
                    ->orWhereHas('plannings', function (Builder $planning) {
                        $planning->where('start_at', '<=', now())
                            ->where('end_at', '>=', now());
                });
            });
    }

    public function scopeSearch(
        $query,
        string $searchTerms,
        array $columns = ['title', 'json_content'],
        bool $addFuzzyness = true
    ) {
        $searchTerms = $this->prepStringForQuery($searchTerms, $addFuzzyness);

        foreach ($searchTerms as $searchTerm) {
            $deFuzzedSearchTerm = $this->deFuzz($searchTerm);
            $query = $query->whereAny($columns, 'LIKE', $searchTerm)
                ->orWhereHas('categories', function ($query) use ($deFuzzedSearchTerm) {
                    $query->where('name', $deFuzzedSearchTerm);
                })->orWhereHas('tags', function ($query) use ($deFuzzedSearchTerm) {
                    $query->where('name', $deFuzzedSearchTerm);
                });
        }

        $query = $query->orderByDesc('created_at');

        return $query;
    }

    public function settings(): HasOne
    {
        return $this->hasOne(PostSettings::class);
    }

    public function plannings(): HasMany
    {
        return $this->hasMany(PostPlannings::class);
    }

    public function communications(): HasMany
    {
        return $this->hasMany(Communication::class);
    }

    public static function getLatests(): Collection
    {
        return Post::with('categories')
            ->published()
            ->orderByDesc('published_at')
            ->limit(6)
            ->get()
            ->each(function ($post) {
                $post->categoryName = $post->categories->first()?->name ?? __('posts.lbl-uncategorized');
            });
    }

    protected function encodedUrl(): Attribute
    {
        return Attribute::make(
            get: fn () =>
                config('app.url')."/post/".$this->slug->name,
        );
    }
}
