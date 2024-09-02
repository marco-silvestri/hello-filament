@php
    $firstHalf = [];
    $secondHalf = [];
    foreach ($section as $post) {
        if (count($firstHalf) < 9) {
            $firstHalf[] = $post;
        } else {
            $secondHalf[] = $post;
        }
    }
@endphp
@if ($section)
    <section>
        <div class="justify-between md:flex md:flex-col">
            <div class="flex flex-col md:flex-row">
                <div class="flex flex-col w-full md:w-3/4">
                    <div class="grid grid-cols-1 md:grid-cols-3">

                        @foreach ($firstHalf as $post)
                            <x-cards.simple-card :post="$post" />
                        @endforeach
                    </div>
                </div>
                <div class="w-full md:w-1/4">
                    <div class="w-full">
                        <div class="flex flex-col">
                            <x-ads.box :n="1" />
                        </div>
                        {{-- <div class="flex flex-col">
                            <x-sections.sponsor-deck />
                        </div> --}}
                    </div>
                    <div class="w-full">
                        <x-cms-custom-navbar.search-sidebar-input :searchKey="$searchKey ?? ''" />

                    </div>

                </div>
            </div>
            @if (count($secondHalf) > 0)
                <x-ads.masthead :n="1" />
                <div class="flex flex-col md:flex-row>
                <div class="flex flex-col w-full md:w-3/4">
                    <div class="grid grid-cols-1 md:grid-cols-3">

                        @foreach ($secondHalf as $post)
                            <x-cards.simple-card :post="$post" />
                        @endforeach

                    </div>
                </div>
                <div class="w-full md:w-1/4">
                    <div class="w-full">
                        <div class="flex flex-col">
                            <x-ads.box :n="2" />
                        </div>
                    </div>
                </div>
        </div>
@endif
<div class="flex flex-col md:flex-row">
    <div class="flex flex-col w-full md:w-3/4">
        @if ($section->total() > $section->perPage())
            <div class="mb-4">
                {{ $section->onEachSide(1)->links() }}
            </div>
        @endif
    </div>
</div>
</div>
</section>
@endif
