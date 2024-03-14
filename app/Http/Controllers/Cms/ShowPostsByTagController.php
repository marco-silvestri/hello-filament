<?php

namespace App\Http\Controllers\Cms;

use App\Models\Tag;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\Cms\HasPostsCaching;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Builder;

class ShowPostsByTagController extends Controller
{
    use HasPostsCaching;

    public function __invoke(Request $request)
    {
        $slug = $request->slug;
        $posts = Cache::remember("posts-tag-$slug", $this->getTtl(), function ()
        use ($slug) {
            return Tag::whereHas('slug', function (Builder $query)
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
