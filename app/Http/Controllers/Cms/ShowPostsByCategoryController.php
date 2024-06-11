<?php

namespace App\Http\Controllers\Cms;

use App\Models\Menu;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Traits\Cms\HasPostsCaching;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Builder;

class ShowPostsByCategoryController extends Controller
{
    use HasPostsCaching;

    public function __invoke(Request $request)
    {
        $slug = $request->slug;
        $category = null;
        $menu = Menu::where('name', 'home-page')
            ->where('is_active', 1)
            ->first();

        $posts = Cache::remember("posts-category-$slug", $this->getTtl(), function ()
        use ($slug, &$category) {
            $category = Category::whereHas('slug', function (Builder $query)
            use ($slug) {
                $query->where('name', $slug);
            })->first();

            if($category){
                return $category
                    ->posts()
                    ->published()
                    ->orderByDesc('published_at')
                    ->paginate(18);
            }

            return abort(404);
        });

        return view('cms.blog.aggregated-posts')
            ->with('group', $category)
            ->with('posts', $posts)
            ->with('menu', $menu);
    }
}
