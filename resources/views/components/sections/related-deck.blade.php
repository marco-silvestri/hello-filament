@if(isset($section) && count($section)>0)
<section>
    <div class="flex w-full px-4 py-8 space-between bg-shade-500">
        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <x-elements.strip-title :title="$title"/>
            <div class="flex flex-col md:grid md:grid-cols-3">
                @foreach ($section as $post)
                    <x-cards.highlight-sm-card :post="$post" :group="$post->categoryName" />
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif
