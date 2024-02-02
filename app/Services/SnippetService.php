<?php

namespace App\Services;

use App\Enums\HookEnum;
use App\Models\Snippet;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class SnippetService
{
    public static function getSnippetsByHook(HookEnum $hook)
    {
        return Snippet::query()
            ->select('payload')
            ->where('hook', $hook->value())
            ->where('status', true)
            ->orderBy('priority')
            ->get();
    }

    public static function fillSnippetsCache(?HookEnum $hook = null)
    {
        if (!$hook) {
            $cache = [];
            foreach (HookEnum::cases() as $case) {
                $cache[] = Cache::remember(
                    "snippets-{$case->value()}", 1800,
                    fn () => self::getSnippetsByHook($case)
                );
            }

            return $cache;
        } else {
            return Cache::remember(
                "snippets-{$hook->value()}", 1800,
                fn () => self::getSnippetsByHook($hook)
            );
        }
    }

    public static function flushSnippetsCache(?HookEnum $hook = null):void
    {
        if (!$hook) {
            foreach (HookEnum::cases() as $case) {
                Cache::forget("snippets-{$case->value()}");
            }
        } else {
            Cache::forget("snippets-{$hook->value()}");
        }
    }

    public static function refreshSnippetsCache(HookEnum $hook):void
    {
        self::flushSnippetsCache($hook);
        self::fillSnippetsCache($hook);
    }

    public static function getStringedSnippets(HookEnum $hook):?string
    {
        $snippets = Cache::get("snippets-{$hook->value()}");

        if($snippets)
        {
            $arrayOfSnippets = $snippets->pluck('payload')->toArray();
            $implodedSnippets = implode(PHP_EOL, $arrayOfSnippets);

            return $implodedSnippets;
        }

        return null;
    }
}
