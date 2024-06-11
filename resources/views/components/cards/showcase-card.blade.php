<section class=" mt-6 mr-8 mb-6 ">
    <div class="flex flex-col min-w-[220px] md:w-[375px]">
        @if($post->slug)
        <a href="{{ route('post', ['slug' => $post->slug->name]) }}">
        @else
        <a href="{{route('home')}}">
        @endif
            <div class="flex items-center justify-center pb-4">
                <x-curator-glider class="object-cover rounded-md" :media="$post->featuredImage?->id" fit="crop-center" height="235"
                    width="375" format="webp" fallback="article_fallback" />
            </div>
            <div>
                <h3 class="pb-2 card--title card--title__base">
                    {{ $post->title }}
                </h3>
            </div>
        </a>
        <x-elements.card-meta-info :post="$post" />
    </div>
</section>
