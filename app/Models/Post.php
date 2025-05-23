<?php

namespace App\Models;

use Spatie\Feed\Feedable;
use Spatie\Feed\FeedItem;
use App\Traits\Cms\HasSlug;
use App\Traits\Cms\HasAuthor;
use App\Models\Cms\PostSettings;
use App\Enums\Cms\PostStatusEnum;
use App\Models\Cms\PostPlannings;
use Awcodes\Curator\Models\Media;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Traits\Cms\HasStringOperations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use CyrildeWit\EloquentViewable\InteractsWithViews;
use CyrildeWit\EloquentViewable\Contracts\Viewable;

class Post extends Model implements Feedable, Viewable
{
    use HasFactory, SoftDeletes, HasSlug, HasAuthor, HasStringOperations, InteractsWithViews;

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
            ->where(function (Builder $q) {
                $q->where('status', PostStatusEnum::PUBLISH)
                    ->where('published_at', '<=', now())
                    ->whereNotNull('published_at');
            })->orWhere(function (Builder $q) {
                $q->where('status', PostStatusEnum::PLANNED)
                    ->whereHas('plannings', function (Builder $planning) {
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

    public function scopeFullTextSearch(
        $query,
        string $searchTerms,
        array $columns = ['title', 'content'],
    ) {
        $searchTerms = $this->prepStringForQuery($searchTerms);
        $termsString = implode(" ",$searchTerms);
        $colsString = implode(", ",$columns);
        $query = $query
            ->selectRaw("*, MATCH($colsString) AGAINST('{$termsString}' IN NATURAL LANGUAGE MODE) as score")
            ->whereRaw("MATCH($colsString) AGAINST(? IN NATURAL LANGUAGE MODE)",
                [$termsString]);

        foreach ($searchTerms as $searchTerm) {
            $query = $query->orWhereHas('categories', function ($query) use ($searchTerm) {
                    $query->where('name', $searchTerm);
                })->orWhereHas('tags', function ($query) use ($searchTerm) {
                    $query->where('name', $searchTerm);
                });
        }

        $query = $query->orderByDesc('score');
        //dd($query->toSql());
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

    public function url($absolute = true): string
    {
        return route('post', ['postId' => $this->id, 'slug' => $this->slug->name], $absolute);
    }

    protected function encodedUrl(): Attribute
    {
        return Attribute::get(fn() => urlencode($this->url()));
    }

    public function toFeedItem(): FeedItem
    {
        return FeedItem::create()
            ->id($this->id)
            ->title($this->title)
            ->summary($this->excerpt)
            ->updated($this->updated_at)
            ->link(config('app.url') . '/' . $this->slug->name)
            ->authorName($this->author?->name ?? "Redazione");
    }

    public static function getAllFeedItems()
    {
        return Post::query()
            ->published()
            ->limit(10)
            ->orderByDesc('created_at')
            ->get();
    }
}
