<?php

namespace App\Http\Controllers\Cms;

use App\Models\Menu;
use App\Models\Post;
use App\Traits\Cms\HasTree;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\Cms\HasPostsCaching;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Builder;

class ShowPostsBySearchController extends Controller
{
    use HasPostsCaching, HasTree;
    public function __invoke(Request $request)
    {

        $searchKey="";
        if (!empty($request->input('kr')) && $request->input('kr')!==null){
            $searchKey=$request->input('kr');
        }
        if (!empty($request->input('k')) && $request->input('k')!==null){
            $searchKey=$request->input('k');
        }

        $menu = Menu::where('name', 'home-page')
            ->where('is_active', 1)
            ->first();

        $posts=Post::query()
            ->with('categories')
            ->with('tags')
            ->published()
            ->search($searchKey)
            ->paginate(18);

        return view('cms.blog.search-posts')
        ->with('k', $searchKey)
        ->with('posts', $posts)
        ->with('menu', $menu);
    }

}
