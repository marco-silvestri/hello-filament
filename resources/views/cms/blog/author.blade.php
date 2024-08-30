<x-layouts.app>
    @section('meta-tags')
    <title>{{ $author->name }}</title>
    @show
    <x-elements.blog-container>
        @section('menu')
            @if ($menu)
                <x-cms-custom-navbar.base :feMenu="$menu" :overrideMenu="true" />
            @endif
        @endsection

        @section('deck')
        <div>
            <h2 class="strip--title strip--title__base">{{ __('common.fld-author') }}: {{$author->name}}</h2>
            @if(config('cms.layout.has_profile_box') === true && $author->profile)
            <div class="font-brand text-[14px]">
                <p> {{$author->profile->description}} </p>
                <div>
                    <span class="uppercase">{{__('author.lbl-url')}}: </span><a href="{{$author->profile->url}}"> {{$author->profile->url}} </a>
                </div>
            </div>
            @endif
        </div>
        <div class="mt-8 text-lg font-bold font-brand-alt">
            {{__('author.lbl-more-articles')}}
        </div>
        <x-sections.listing-deck :section="$posts" />
        @endsection

    </x-elements.blog-container>
</x-layouts.app>

