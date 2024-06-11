<x-layouts.app>
    <x-elements.blog-container>
        @section('menu')
            @if ($menu)
                <x-cms-custom-navbar.base :feMenu="$menu" :overrideMenu="true" />
            @endif
        @endsection

        @section('deck')
            @if(get_class($group) == 'App\Models\Category')
                {{ Breadcrumbs::render('category', $group) }}
            @elseif(get_class($group) == 'App\Models\Tag')
                {{ Breadcrumbs::render('tag', $group) }}
            @endif
        <x-sections.listing-deck :section="$posts" />
        @endsection

    </x-elements.blog-container>
</x-layouts.app>
