@if(isset($section['posts']) && count($section['posts'])>0)
<section>
    <div class="hidden md:flex px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="w-2/3">
            <x-cards.hero-huge-card :post="$section['posts'][0]" :groupTitle="$section['title']"/>
        </div>
        <div class="w-1/3">
            @foreach ($section['posts'] as $post)
            @if($loop->first)@else
            <x-cards.hero-aside-card :post="$post" :groupTitle="$section['title']" />
            @endif
            @endforeach
        </div>
    </div>
    <div class="flex flex-col items-center justify-center w-full md:hidden">
        @foreach ($section['posts'] as $post)
        <x-cards.hero-mobile-card :post="$post" :groupTitle="$section['title']" />
        @endforeach
    </div>
</section>
@endif
