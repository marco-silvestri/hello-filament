<x-layouts.app>
    @section('title')
        <title>{{ config('app.name') . ' - ' . $group->name }}</title>
    @endsection
    <x-elements.blog-container>
        @section('menu')
            @if ($menu)
                <x-cms-custom-navbar.base :feMenu="$menu" :overrideMenu="true" />
            @endif
        @endsection

        @section('deck')
            @if (config('cms.layout.has_breadcrumbs'))
                @if (get_class($group) == 'App\Models\Category')
                    {{ Breadcrumbs::render('category', $group) }}
                @elseif(get_class($group) == 'App\Models\Tag')
                    {{ Breadcrumbs::render('tag', $group) }}
                @endif
            @endif
            <x-sections.listing-deck :section="$posts" />
        @endsection

    </x-elements.blog-container>
</x-layouts.app>
