<x-layouts.app>

    @section('title')
        <title>{{ $page->title }}</title>
    @endsection

    @isset($isPreview)
        <div class="sticky top-0 z-50 w-full py-2 text-center text-white opacity-80 bg-brand-500">
            {{ __('posts.lbl-preview') }}
        </div>
    @endisset
    <x-elements.blog-container>
        @section('menu')
            @if ($menu)
                <x-cms-custom-navbar.base :feMenu="$menu" :overrideMenu="true" />
            @endif
        @endsection

        @section('deck')
            @if (config('cms.layout.has_breadcrumbs'))
                {{ Breadcrumbs::render('page', $page) }}
            @endif
            <div class="flex justify-between">
                <div class="mb-12 w-4/4 md:w-3/4 md:pr-4">
                    <div class="mt-4 mb-2">
                        <h1 class="mt-4 mb-6 post--title post--title__base">
                            {{ $page->title }}
                        </h1>
                    </div>
                    <div>
                        @if ($page->json_content)
                            @foreach ($page->json_content as $dataBlock)
                                {!! \App\Services\BlockLoader::renderDataBlock($dataBlock) !!}
                            @endforeach
                        @endif
                    </div>
                </div>
                <div class="flex flex-row w-full mx-2 md:w-1/4">
                    <x-sections.sponsor-deck />
                </div>
            </div>
            </div>
        @endsection
    </x-elements.blog-container>
</x-layouts.app>
