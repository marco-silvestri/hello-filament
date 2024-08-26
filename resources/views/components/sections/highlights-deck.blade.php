@if(isset($section['posts']) && count($section['posts'])>0)
<section>
    <div class="flex w-full px-4 py-8 space-between bg-shade-500 shadow-y">
        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <x-elements.strip-title :title="$title"/>
            <div class="flex flex-col md:grid md:grid-cols-3">
                @foreach ($section['posts'] as $post)
                    <x-cards.highlight-sm-card :post="$post" :group="$section['title']" />
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif
