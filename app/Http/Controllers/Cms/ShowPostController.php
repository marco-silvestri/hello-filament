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

    public function __invoke($postId, $slug = null)
    {
        $post = Cache::remember("post-$postId", $this->getTtl(), function () use ($postId) {
            $with = ['settings', 'categories', 'tags'];

            if (config('app.comments')) {
                $with = $with + ['comments' => fn($query) => $query->approved()];
            }

            return Post::query()
                ->with($with)
                ->published()
                ->find($postId);
        });

        abort_unless($post, Response::HTTP_NOT_FOUND);

        // check real slug and redirect if different from $slug
        if ($post->slug->name !== $slug) {
            return redirect()->to($post->url(), Response::HTTP_MOVED_PERMANENTLY);
        }

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

        if (config('app.comments')) {
            $post->commentsCount = count($post->comments);
            $post->comments = $this->buildHierarchyTree($post->comments);
        }

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

        return view('cms.blog.post')
            ->with('menu', $menu)
            ->with('post', $post)
            ->with('relatedPosts', $relatedPosts)
            ->with('prevPost', $prevPost)
            ->with('nextPost', $nextPost);
    }
}
