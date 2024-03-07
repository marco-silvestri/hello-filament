<?php

use App\Http\Middleware\Cms\TrackVisits;
use App\Models\Post;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Illuminate\Database\Eloquent\Builder;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


Route::get('/{slug}', function(string $slug){

    $post = Cache::remember("post-$slug", 60 * 60 * 8, function ()
    use ($slug)
    {
        return Post::whereHas('slug', function (Builder $query )  use ($slug)  {
            $query->where('name', $slug);
        })->first();
});

    return view('public')
        ->with('post', $post);
})->middleware(TrackVisits::class);

require __DIR__ .'/cms/web.php';
