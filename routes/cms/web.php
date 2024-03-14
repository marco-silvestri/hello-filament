<?php


use Illuminate\Support\Facades\Route;
use App\Http\Middleware\Cms\TrackVisits;
use App\Http\Controllers\Cms\ShowPostsController;
use App\Http\Controllers\Cms\ShowPostsByTagController;
use App\Http\Controllers\Cms\ShowPostsByAuthorController;
use App\Http\Controllers\Cms\ShowPostsByCategoryController;

Route::group([], function () {
    Route::get('/{slug}',ShowPostsController::class);
    Route::get('/tag/{slug}', ShowPostsByTagController::class);
    Route::get('/category/{slug}', ShowPostsByCategoryController::class);
    Route::get('/author/{slug}', ShowPostsByAuthorController::class);
})->middleware(TrackVisits::class);
