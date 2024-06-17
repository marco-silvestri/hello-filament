<?php

namespace App\Http\Controllers\Cms;

use App\Models\Menu;
use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ShowPostPreviewController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Post $post)
    {
        $menu = Menu::where('name', 'home-page')
            ->where('is_active', 1)
            ->first();


        return view('cms.blog.post')
            ->with('menu', $menu)
            ->with('post', $post)
            ->with('isPreview', true);
    }
}
