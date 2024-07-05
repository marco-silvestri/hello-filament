<x-layouts.app>
    @if ($menu)
        <x-cms-custom-navbar.base :feMenu="$menu" :overrideMenu="true" />
    @endif

    @if (session('subscription-success'))
        <div class="alert alert-success">
            {{ session('subscription-success') }}
        </div>
    @endif

    @if (session('subscription-error'))
        <div class="alert alert-success">
            {{ session('subscription-error') }}
        </div>
    @endif

    <x-sections.hero-deck :section="$header" />
    <x-sections.highlights-deck :section="$highlight" :title="__('home-page.highlights-title')" />
    <div class="px-4 mx-auto my-8 max-w-7xl sm:px-6 lg:px-8">
        <x-elements.strip-title :title="__('home-page.showcase-title')" />
        <div class="justify-between md:flex md:flex-row">
            <div class="flex flex-row w-full md:w-3/4">
                <x-sections.showcase-deck :section="$showcase" />
            </div>
            <div class="flex flex-col w-full md:w-1/4">
                <x-sections.sponsor-deck />
                <x-cms-custom-navbar.search-sidebar-input :searchKey="$searchKey" />
            </div>
        </div>
    </div>

    <x-sections.cards-deck :section="$highlightedStrip" :isHighlighted="true" />

    @foreach ($strips as $strip)
        <x-sections.cards-deck :section="$strip" />
    @endforeach
    <x-footer />
</x-layouts.app>
