@if(isset($section['posts']) && count($section['posts'])>0)
<section>
    <div class="flex w-full space-between">
        <div>
            <div class="grid grid-cols-1 md:grid-cols-2">
                @foreach ($section['posts'] as $post)
                    <x-cards.showcase-card :post="$post" />
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif
