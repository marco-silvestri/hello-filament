@if($section)
    <section>
        <div class="justify-between md:flex md:flex-row">
            <div class="flex flex-col w-full md:w-3/4">
                <div class="grid grid-cols-1 md:grid-cols-3">
                    @foreach ($section as $post)
                        <x-cards.simple-card :post="$post" />
                    @endforeach
                    
                </div>
                @if ($section->total() > $section->perPage())
                <div class="mb-4">
                    {{$section->onEachSide(1)->links()}}
                 </div>
                @endif
            </div>
            <div class="w-full md:w-1/4">
                <div class="w-full">
                    <x-sections.sponsor-deck />
                </div>
                <div class="w-full">
                <x-cms-custom-navbar.search-sidebar-input :searchKey="$searchKey"/>
                </div>
            
            </div>
        </div>
    </section>
@endif
