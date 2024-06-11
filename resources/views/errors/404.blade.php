<x-layouts.app>
    <x-cms-custom-navbar.base :overrideMenu="true" :feMenu="(new \App\Models\Menu())->getNamedMenu('home-page')" />
    <div class="flex flex-col items-center justify-center mx-auto max-w-7xl">
        <p class="flex-grow h-64 py-16 text-2xl font-brand text-display-500">
            {{__('errors.404')}}
        </p>

    </div>
    <x-sections.related-deck :section="\App\Models\Post::getLatests()" :title="__('posts.lbl-fallback')" />
    <x-footer :hasSitemap="false"/>
</x-layouts.app>

