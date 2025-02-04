<?php

namespace App\Providers;

use App\Enums\Cms\HookEnum;
use App\Models\Snippet;
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
        View::composer(['components.layouts.app'], function ($view) {
            $snippets = Cache::remember("snippets", 1800,
                fn () => Snippet::query()
                    ->select('payload', 'hook')
                    ->where('status', true)
                    ->orderBy('priority')
                    ->get()
            );

            $headSnippets = $snippets->where('hook', HookEnum::HEAD)
                ->pluck('payload')
                ->implode(PHP_EOL);
            $bodySnippets = $snippets->where('hook', HookEnum::BODY)
                ->pluck('payload')
                ->implode(PHP_EOL);
            $footerSnippets = $snippets->where('hook', HookEnum::FOOTER)
                ->pluck('payload')
                ->implode(PHP_EOL);

            $view->with('headSnippets', $headSnippets);
            $view->with('bodySnippets', $bodySnippets);
            $view->with('footerSnippets', $footerSnippets);
        });
    }
}
