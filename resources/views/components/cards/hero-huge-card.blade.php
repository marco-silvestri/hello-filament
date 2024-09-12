<section class="p-2">
    <div class="flex flex-col w-full py-12">
        @if($post->slug)
        <a href="{{ $post->url() }}">
        @else
        <a href="{{route('home')}}">
        @endif
            <div class="pb-4 max-h-[500px] flex justify-center items-center">
                <x-curator-glider class="object-cover rounded-md" :media="$post->featuredImage?->id" fit="crop-center" width="626"
                    height="362" format="webp" fallback="article_fallback" />
            </div>
            <div>
                <x-elements.group-title :title="$groupTitle" />
                <h2 class="pb-2 card--title card--title__large">
                    {{ $post->title }}
                </h2>

                <p class=""> {{html_entity_decode(\Illuminate\Support\Str::limit(strip_tags($post->content), 200))}}</p>
            </div>
        </a>
    </div>
</section>
