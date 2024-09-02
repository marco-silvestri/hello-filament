@props([
    'isHighlighted' => false,
    'hasAdsBox'=>false,
    'section' => [
        'title' => 'Untitled',
        'posts' => [],
    ],
])

@if($section['posts'] && count($section['posts'])>0)
<aside>
    <div class="w-[calc(100%-2rem)] flex space-between py-4
        @if($isHighlighted)
        bg-shade-500 shadow-y
        @endif">
     
        <div class="w-full px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">    
        <x-elements.strip-title :title="$section['title']" anchor="{{config('app.url') .'/'. $section['groupable_type'] .'/'. $section['slug']}}"/>
        <x-elements.carousel :section="$section['posts']" :isHighlighted="$isHighlighted" :hasAdsBox="$hasAdsBox"/>
        </div>
    </div>
</aside>
@endif

