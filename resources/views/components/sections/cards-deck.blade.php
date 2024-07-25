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
    <div class="w-full flex space-between py-4
        @if($isHighlighted)
        bg-shade-500
        @endif">
     
        <div class="w-full px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
        @if (!$isHighlighted)
        <div class="md:w-3/4">
        @endif
        <x-elements.strip-title :title="$section['title']" anchor="{{config('app.url') .'/'. $section['groupable_type'] .'/'. $section['slug']}}"/>
        @if (!$isHighlighted)
        </div>
        @endif

        <x-elements.carousel :section="$section['posts']" :isHighlighted="$isHighlighted" :hasAdsBox="$hasAdsBox"/>
        </div>
    </div>
</aside>
@endif

