<x-layouts.app>

    @section('title')
    <title>{{ config('app.name') }}</title>
    @endsection
    <x-ads.leaderboard />
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
    <x-ads.masthead :n="1" />
    @if(isset($showcase['posts']) && count($showcase['posts']) > 0)
        <div class="px-4 mx-auto my-8 max-w-7xl sm:px-6 lg:px-8">
            <x-elements.strip-title :title="__('home-page.showcase-title')" />
            <div class="justify-between md:flex md:flex-row">
                <div class="flex flex-col w-full md:w-3/4">
                    <x-sections.showcase-deck :section="$showcase" />
                </div>
                <div class="flex flex-col w-full md:w-1/4">
                    <x-widgets.magazine-subscription />
                    <x-cms-custom-navbar.search-sidebar-input :searchKey="$searchKey" />
                    <x-ads.box :n="2" />
                    <x-widgets.most-read />
                </div>
            </div>
            <x-ads.masthead :n="2" />
            <div class="justify-between md:flex md:flex-row">
                <div class="flex flex-col w-full md:w-3/4">
                    <x-sections.cards-deck :section="$highlightedStrip" :isHighlighted="true" />
                    @foreach ($strips as $strip)
                        <x-sections.cards-deck :section="$strip" />
                    @endforeach
                </div>
                <div class="flex flex-col w-full md:w-1/4">
                    <x-ads.box :n="3" />
                    <x-sections.sponsor-deck />
                </div>
            </div>
        </div>
    @endif
    <x-ads.masthead :n="3" />
    <x-footer />
</x-layouts.app>