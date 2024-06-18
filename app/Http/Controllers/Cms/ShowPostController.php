<?php

namespace App\Http\Controllers\Cms;

use App\Models\Menu;
use App\Models\Post;
use App\Traits\Cms\HasTree;
use Illuminate\Http\Request;
use App\Traits\Cms\HasPostsCaching;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpFoundation\Response;

class ShowPostController extends Controller
{
    use HasPostsCaching, HasTree;
    public function __invoke(Request $request)
    {
        $slug = $request->slug;

        $post = Cache::remember("post-$slug", $this->getTtl(), function ()
        use ($slug) {
            return Post::with([
                'settings',
                'categories',
                'tags',
                'comments' => fn ($query) => $query->approved()
            ])
                ->published()
                ->whereHas('slug', function (Builder $query)
                use ($slug) {
                    $query->where('name', $slug);
                })->first();
        });

        abort_unless($post, Response::HTTP_NOT_FOUND);

        $menu = Menu::where('name', 'home-page')
            ->where('is_active', 1)
            ->first();

        $relatedPosts = Cache::remember("related-$slug", $this->getTtl(), function () use ($post) {
            return $post->categories()->limit(5)->get()->map(function ($category) {
                return $category->posts()
                    ->published()
                    ->limit(6)
                    ->get()
                    ->each(function ($post) use ($category) {
                        $post->categoryName = $category->name;
                    });
            })->flatten()->unique('id');
        });

        $relatedPosts = $relatedPosts->shuffle();
        $relatedPosts = $relatedPosts->take(6);

        $post->commentsCount = count($post->comments);

        $prevPost = Cache::remember("prev-$slug", $this->getTtl(), function () use ($post) {
            return Post::with('slug')
                ->where('published_at', '<', $post->published_at)
                ->orderByDesc('id')
                ->first();
        });

        $nextPost = Cache::remember("next-$slug", $this->getTtl(), function () use ($post) {
            return Post::with('slug')
                ->where('published_at', '>', $post->published_at)
                ->orderBy('id')
                ->first();
        });

        $post->comments = $this->buildHierarchyTree($post->comments);
        return view('cms.blog.post')
            ->with('menu', $menu)
            ->with('post', $post)
            ->with('relatedPosts', $relatedPosts)
            ->with('prevPost', $prevPost)
            ->with('nextPost', $nextPost);
    }
}
