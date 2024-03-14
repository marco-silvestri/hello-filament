<?php

namespace App\Http\Controllers\Cms;

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
        $posts = Cache::remember("posts-category-$slug", $this->getTtl(), function ()
        use ($slug) {
            return Category::whereHas('slug', function (Builder $query)
            use ($slug) {
                $query->where('name', $slug);
            })->first()
                ->posts()
                ->published()
                ->get();
        });
        return view('cms.blog.aggregated-posts')
            ->with('posts', $posts);
    }
}
