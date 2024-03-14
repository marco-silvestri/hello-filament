<?php

namespace App\Http\Controllers\Cms;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\Cms\HasPostsCaching;
use App\Traits\Cms\HasTree;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Builder;

class ShowPostsController extends Controller
{
    use HasPostsCaching, HasTree;
    public function __invoke(Request $request)
    {
        $slug = $request->slug;
        $post = Cache::remember("post-$slug", $this->getTtl(), function ()
        use ($slug) {
            return Post::with(['comments' => function($query){
                $query->approved();
            }])
                ->published()
                ->whereHas('slug', function (Builder $query)
                use ($slug) {
                    $query->where('name', $slug);
                })->first();
        });

        $post->comments = $this->buildHierarchyTree($post->comments);

        return view('cms.blog.post')
            ->with('post', $post);
    }
}
