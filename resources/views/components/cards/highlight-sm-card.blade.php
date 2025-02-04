@props([
    'post' => null,
    'group' => null,
    'shownNumber' => null,
])
<div class="flex items-center mt-4 mb-4">
    <div class="basis-[85px] mr-4 relative">
        <a href="{{ $post->url() }}">
            <x-curator-glider class="object-cover rounded-md" :media="$post->featuredImage" fit="crop-center" format="webp"
                height="85" width="85" fallback="article_fallback" />
        </a>
        @if($shownNumber)
            <div class="absolute bottom-0 right-0 px-2 pt-1 text-lg font-black bg-white rounded-tl-md text-brand-500">
                {{$shownNumber}}
            </div>
        @endif
    </div>
    <div class="basis-2/3">
        <x-elements.group-title :title="$group" />
        <h3 class="pb-2 card--title card--title__small">
            <a href="{{ $post->url() }}">
                {{ $post->title }}
            </a>
        </h3>
        <x-elements.card-meta-info :post="$post" />
    </div>
</div>
