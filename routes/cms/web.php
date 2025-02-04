<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\Cms\TrackVisits;
use Filament\Http\Middleware\Authenticate;
use App\Http\Controllers\Cms\HomePageController;
use App\Http\Controllers\Cms\ShowPageController;
use App\Http\Controllers\Cms\ShowPostController;
use App\Http\Controllers\Cms\ShowPostsByTagController;
use App\Http\Controllers\Cms\ShowPostPreviewController;
use App\Http\Controllers\Cms\ShowPostsByAuthorController;
use App\Http\Controllers\Cms\ShowPostsBySearchController;
use App\Http\Controllers\Cms\ShowPostsByCategoryController;
use App\Livewire\Cms\NewsletterSubscription;

Route::get('test', NewsletterSubscription::class);
Route::get('/', HomePageController::class)->name('home');

Route::middleware(TrackVisits::class)->group(function () {
    Route::get('/post/{postId}/{slug?}', ShowPostController::class)->name('post')
        ->where(['postId' => '[0-9]+', 'slug' => '.*']);
});

Route::get('/cerca', ShowPostsBySearchController::class)->name('search');
Route::get('/tag/{slug}', ShowPostsByTagController::class)->name('tag');
Route::get('/categoria/{slug}', ShowPostsByCategoryController::class)->name('category');
Route::get('/autore/{slug}', ShowPostsByAuthorController::class)->name('author');
Route::get('/pagina/{slug}', ShowPageController::class)->name('page');


Route::get('/admin/preview/{post}', ShowPostPreviewController::class)->name('preview')
    ->middleware(Authenticate::class);
