<?php

namespace App\Http\Controllers\Cms;

use App\Models\Menu;
use App\Models\Page;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpFoundation\Response;

class ShowPageController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $slug = $request->slug;

        $page = Page::whereHas('slug', function (Builder $query)
                use ($slug) {
                    $query->where('name', $slug);
                })->first();

        abort_unless($page, Response::HTTP_NOT_FOUND);

        $menu = Menu::where('name', 'home-page')
            ->where('is_active', 1)
            ->first();

        return view('cms.blog.page')
            ->with('menu', $menu)
            ->with('page', $page);
    }
}
