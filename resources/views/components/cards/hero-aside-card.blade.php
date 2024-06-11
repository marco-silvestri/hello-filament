<section>
    @if($post->slug)
    <a href="{{ route('post', ['slug' => $post->slug->name]) }}">
    @else
    <a href="{{route('home')}}">
    @endif
    <div class="flex mt-4 mb-4 mr-4 ">
        <div class="pb-4 basis-[168px]  justify-left items-center">
            <x-curator-glider class="object-cover rounded-md" :media="$post->featuredImage?->id" fit="crop-center" format="webp"
                width="168" height="150" fallback="article_fallback" />
        </div>
        <div class="basis-2/3 pl-2">
            <x-elements.group-title :title="$groupTitle" />
            <h3 class="pb-2 card--title card--title__base">
                {{ $post->title }}
            </h3>
        </div>
    </div>
</a>
</section>

