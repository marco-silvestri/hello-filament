<?php

use App\Http\Controllers\Api\Cms\InternalNewsletterApi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('newsletter')->group(function(){
    Route::post('token', [InternalNewsletterApi::class, 'getToken'])->name('newsletter.token');
    Route::post('get', [InternalNewsletterApi::class, 'getSentNewsletters'])->name('newsletter.get');
    Route::post('preview', [InternalNewsletterApi::class, 'getPreviewNewsletter'])->name('newsletter.preview');
    Route::post('update', [InternalNewsletterApi::class, 'updateNewsletter'])->name('newsletter.update');
})->name('newsletter');
