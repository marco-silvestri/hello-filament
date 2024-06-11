@props([
    'isHighlighted' => false,
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
        <x-elements.strip-title :title="$section['title']"/>
            <x-elements.carousel :section="$section['posts']"/>
        </div>
    </div>
</aside>
@endif

