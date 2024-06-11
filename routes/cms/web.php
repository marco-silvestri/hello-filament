<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\Cms\TrackVisits;
use App\Http\Controllers\Cms\HomePageController;
use App\Http\Controllers\Cms\ShowPostController;
use App\Http\Controllers\Cms\ShowPostsByTagController;
use App\Http\Controllers\Cms\ShowPostsByAuthorController;
use App\Http\Controllers\Cms\ShowPostsBySearchController;
use App\Http\Controllers\Cms\ShowPostsByCategoryController;

Route::get('/', HomePageController::class)->name('home');

Route::group([], function () {
    Route::get('/search//', ShowPostsBySearchController::class)->name('search');
    Route::get('/{slug}',ShowPostController::class)->name('post');
    Route::get('/tag/{slug}', ShowPostsByTagController::class)->name('tag');
    Route::get('/category/{slug}', ShowPostsByCategoryController::class)->name('category');
    Route::get('/author/{slug}', ShowPostsByAuthorController::class)->name('author');
   
    Route::get('/page/{slug}', fn() => dd('Not implemented yet'));
})->middleware(TrackVisits::class);
