<?php

namespace App\Http\Controllers\Cms;

use App\Models\Menu;
use Illuminate\Http\Request;
use App\Models\HomePageSetting;
use App\Traits\Cms\HasPostsCaching;
use App\Enums\Cms\DisplayableAsEnum;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class HomePageController extends Controller
{
    use HasPostsCaching;

    public function __invoke()
    {
        $menu = Menu::getNamedMenu('home-page');
        $posts = Cache::remember("home-page-posts", 1, function (){
            $groups = HomePageSetting::with(['groupable'])->orderBy('order_by')->get();

            $header =  $groups
                ->where('displayable_as', \App\Enums\Cms\DisplayableAsEnum::HEADER->getValue())
                ->filter(fn($el)=> $el->groupable)
                ->map(fn($header)=> [
                    'title' => $header->groupable->name,
                    'posts' => $header->getPosts(3),
                    'slug' => $header->groupable->slug->name,
                    'groupable_type' => $header->groupable->getHumanizedSluggableType(),
                ])
                ->first();
            $highlight = $groups
                ->where('displayable_as', \App\Enums\Cms\DisplayableAsEnum::HIGHLIGHT->getValue())
                ->filter(fn($el)=> $el->groupable)
                ->map(fn($highlight)=> [
                    'title' => $highlight->groupable->name,
                    'posts' => $highlight->getPosts(6),
                    'slug' => $highlight->groupable->slug->name,
                    'groupable_type' => $highlight->groupable->getHumanizedSluggableType(),
                ])
                ->first();
            $showcase =  $groups
                ->where('displayable_as', \App\Enums\Cms\DisplayableAsEnum::SHOWCASE->getValue())
                ->map(fn($showcase)=>['posts'=>$showcase->getPosts(8)])
                ->first();

            $hStrip = $groups
                ->where('displayable_as', \App\Enums\Cms\DisplayableAsEnum::HIGHLIGHTED_STRIP->getValue())
                ->filter(fn($el)=> $el->groupable)
                ->map(fn($hStrip)=> [
                    'title' => $hStrip->groupable->name,
                    'posts' => $hStrip->getPosts(10),
                    'slug' => $hStrip->groupable->slug->name,
                    'groupable_type' => $hStrip->groupable->getHumanizedSluggableType(),])
                ->first();
            $strips = $groups
                ->where('displayable_as', \App\Enums\Cms\DisplayableAsEnum::STRIP->getValue())
                ->filter(fn($el)=> $el->groupable)
                ->map(fn($strip)=> [
                    'title' => $strip->groupable->name,
                    'posts' => $strip->getPosts(3),
                    'slug' => $strip->groupable->slug->name,
                    'groupable_type' => $strip->groupable->getHumanizedSluggableType(),])
                ->values();

            return [
                'header' => $header,
                'showcase' => $showcase,
                'highlight' => $highlight,
                'highlighted-strip' => $hStrip,
                'strips' => $strips
            ];
        });

        return view('cms.blog.home')
            ->with('menu', $menu)
            ->with('searchKey','')
            ->with('header', $posts['header'])
            ->with('showcase', $posts['showcase'])
            ->with('highlight', $posts['highlight'])
            ->with('highlightedStrip', $posts['highlighted-strip'])
            ->with('strips', $posts['strips']);
    }
}
