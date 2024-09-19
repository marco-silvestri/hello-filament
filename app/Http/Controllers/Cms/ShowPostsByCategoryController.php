<?php

namespace App\Http\Controllers\Cms;

use App\Models\Menu;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Traits\Cms\HasPostsCaching;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpFoundation\Response;

class ShowPostsByCategoryController extends Controller
{
    use HasPostsCaching;

    public function __invoke(Request $request)
    {
        $slug = $request->slug;
        $category = null;

        $posts = Cache::flexible("posts-category-$slug", $this->getFlexibleTtl(), function ()
        use ($slug, &$category) {
            $category = Category::whereHas('slug', function (Builder $query)
            use ($slug) {
                $query->where('name', $slug);
            })->first();

            abort_unless($category, Response::HTTP_NOT_FOUND);

            return $category
                ->posts()
                ->published()
                ->orderByDesc('published_at')
                ->paginate(18);
        });

        $menu = Menu::where('name', 'home-page')
            ->where('is_active', 1)
            ->first();

        return view('cms.blog.aggregated-posts')
            ->with('group', $category)
            ->with('posts', $posts)
            ->with('menu', $menu);
    }
}
