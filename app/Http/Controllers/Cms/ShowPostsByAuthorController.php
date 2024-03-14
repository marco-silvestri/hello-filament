<?php

namespace App\Http\Controllers\Cms;

use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\Cms\HasPostsCaching;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Builder;

class ShowPostsByAuthorController extends Controller
{
    use HasPostsCaching;

    public function __invoke(Request $request)
    {
        $slug = $request->slug;
        $author = Cache::remember("author-$slug", $this->getTtl(), function () use ($slug) {
            return User::whereHas('slug', function (Builder $query)  use ($slug) {
                $query->where('name', $slug);
            })->with(['profile', 'posts' => function ($query) {
                $query->published();
            }])->first();
        });

        return view('cms.blog.author')
            ->with('author', $author);
    }
}
