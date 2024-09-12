<section>
    @if($post->slug)
    <a href="{{ $post->url() }}">
    @else
    <a href="{{route('home')}}">
    @endif
    <div class="flex justify-center mt-2 mb-2 min-h-[250px] max-h-[250px]">
        <div class="relative flex items-center justify-center pb-4 rounded-b-md">
            <div class="absolute w-full pt-2 pb-8 pl-4 rounded-b-lg bottom-2 bg-display-950 bg-opacity-40">
            <x-elements.group-title :title="$groupTitle" altTextColorClass="text-white"/>
                <h3 class="text-white card--title card--title__base">
                    {{ $post->title }}
                </h3>
            </div>
            <x-curator-glider class="object-cover rounded-lg" :media="$post->featuredImage?->id" fit="crop-center" format="webp"
                width="400" height="250" fallback="article_fallback" />
        </div>
    </div>
</a>
</section>
