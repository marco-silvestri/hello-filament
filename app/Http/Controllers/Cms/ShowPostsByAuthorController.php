<?php

namespace App\Http\Controllers\Cms;

use Exception;
use App\Models\Menu;
use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\Cms\HasPostsCaching;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpFoundation\Response;

class ShowPostsByAuthorController extends Controller
{
    use HasPostsCaching;

    public function __invoke(Request $request)
    {
        $slug = $request->slug;

        $author = Cache::remember("author-$slug", $this->getTtl(), function () use ($slug) {
            return User::whereHas('slug', function (Builder $query)  use ($slug) {
                $query->where('name', $slug);
            })->with(['profile'])->first();
        });

        abort_unless($author, Response::HTTP_NOT_FOUND);

        $menu = Menu::where('name', 'home-page')
            ->where('is_active', 1)
            ->first();

        $posts = $author->posts()->published()->paginate(9);

        return view('cms.blog.author')
            ->with('author', $author)
            ->with('posts', $posts)
            ->with('menu', $menu);
    }
}
