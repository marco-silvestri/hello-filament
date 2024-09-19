<?php

namespace App\Http\Controllers\Cms;

use App\Models\Tag;
use App\Models\Menu;
use Illuminate\Http\Request;
use App\Traits\Cms\HasPostsCaching;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpFoundation\Response;

class ShowPostsByTagController extends Controller
{
    use HasPostsCaching;

    public function __invoke(Request $request)
    {
        $slug = $request->slug;
        $tag = null;

        $posts = Cache::flexible("posts-tag-$slug", $this->getFlexibleTtl(), function ()
        use ($slug, &$tag) {
            $tag = Tag::whereHas('slug', function (Builder $query)
            use ($slug) {
                $query->where('name', $slug);
            })->first();

            abort_unless($tag, Response::HTTP_NOT_FOUND);

            return $tag
                ->posts()
                ->published()
                ->orderByDesc('published_at')
                ->paginate(18);
        });

        $menu = Menu::where('name', 'home-page')
            ->where('is_active', 1)
            ->first();

        return view('cms.blog.aggregated-posts')
            ->with('group', $tag)
            ->with('posts', $posts)
            ->with('menu', $menu);
    }
}
