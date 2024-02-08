<?php

namespace App\Providers;

use App\Enums\Cms\HookEnum;
use App\Services\SnippetService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;

class CmsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer(['components.layouts.post'], function ($view) {

            $headSnippets = SnippetService::getStringedSnippets(HookEnum::HEAD);
            $bodySnippets = SnippetService::getStringedSnippets(HookEnum::BODY);
            $footerSnippets = SnippetService::getStringedSnippets(HookEnum::FOOTER);

            $view->with('headSnippets', $headSnippets);
            $view->with('bodySnippets', $bodySnippets);
            $view->with('footerSnippets', $footerSnippets);
        });
    }
}
