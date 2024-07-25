@props([
    'post' => null,
    'group' => null,
    'shownNumber' => null,
])
<section>
    <div class="flex items-center w-full mt-4 mb-4 mr-4">
        @if($post->slug)
        <a href="{{ route('post', ['slug' => $post->slug->name]) }}">
        @else
        <a href="{{route('home')}}">
        @endif
            <div class="basis-[85px] mr-4 relative">
                <x-curator-glider class="object-cover rounded-md" :media="$post->featuredImage?->id" fit="crop-center" format="webp"
                    height="85" width="85" fallback="article_fallback" />
                    @if($shownNumber)
                    <div class="absolute bottom-0 right-0 px-2 pt-1 text-lg font-black bg-white rounded-tl-md text-brand-500">
                        {{$shownNumber}}
                    </div>
                    @endif
            </div>
            <div class="basis-2/3">
                <x-elements.group-title :title="$group" />
                <h3 class="pb-2 card--title card--title__small">
                    {{ $post->title }}
                </h3>
                <x-elements.card-meta-info :post="$post" />
            </div>
        </a>
    </div>
</section>
