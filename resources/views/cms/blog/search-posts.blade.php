
<x-layouts.app>
    @section('title')
    <title>{{ config('app.name') . ' - ' . trim($k) }}</title>
    @endsection
    <x-elements.blog-container>
        @section('menu')
            @if ($menu)
                <x-cms-custom-navbar.base :feMenu="$menu" :searchKey="$k" :overrideMenu="true" />
            @endif
        @endsection

        @section('deck')

        <x-sections.listing-deck :section="$posts" :searchKey="$k" />
        @endsection

    </x-elements.blog-container>
</x-layouts.app>
