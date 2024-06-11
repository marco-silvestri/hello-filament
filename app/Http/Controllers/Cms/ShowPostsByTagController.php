<?php

namespace App\Http\Controllers\Cms;

use App\Models\Tag;
use App\Models\Menu;
use Illuminate\Http\Request;
use App\Traits\Cms\HasPostsCaching;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Builder;

class ShowPostsByTagController extends Controller
{
    use HasPostsCaching;

    public function __invoke(Request $request)
    {
        $slug = $request->slug;
        $tag = null;
        $menu = Menu::where('name', 'home-page')
            ->where('is_active', 1)
            ->first();

        $posts = Cache::remember("posts-tag-$slug", $this->getTtl(), function ()
        use ($slug, &$tag) {
            $tag = Tag::whereHas('slug', function (Builder $query)
            use ($slug) {
                $query->where('name', $slug);
            })->first();

            if ($tag) {
                return $tag
                    ->posts()
                    ->published()
                    ->orderByDesc('published_at')
                    ->paginate(18);
            }

            return abort(404);
        });

        return view('cms.blog.aggregated-posts')
            ->with('group', $tag)
            ->with('posts', $posts)
            ->with('menu', $menu);
    }
}
