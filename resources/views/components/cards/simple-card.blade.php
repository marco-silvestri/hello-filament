<section>
    <div class="flex flex-col w-full md:w-[241px] mt-6 mr-6 mb-6 ">
        @if($post->slug)
        <a href="{{ route('post', ['slug' => $post->slug->name]) }}">
        @else
        <a href="{{route('home')}}">
        @endif
            <div class="pb-4 h-[200px] flex justify-center items-center">
                <x-curator-glider class="object-cover rounded-md" :media="$post->featuredImage?->id" fit="crop-center" height="167"
                    width="241" format="webp" fallback="article_fallback" />
            </div>
            <div>
                <h3 class="pb-2 card--title card--title__base text-center sm:text-left">
                    {{ $post->title }}
                </h3>
            </div>
        </a>
        <div class=" text-center sm:text-left">
        <x-elements.card-meta-info :post="$post" />
        </div>
    </div>
</section>
