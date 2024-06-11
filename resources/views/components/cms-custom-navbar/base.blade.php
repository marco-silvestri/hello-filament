@props([
    'feMenu' => null,
    'overrideMenu' => false,
    'searchKey'=>''
])

<nav x-data="{ menuOpen: false }" class="block w-full md:hidden">
    <div class="flex justify-between w-full">
        <button @click="menuOpen = !menuOpen" class="ml-4 border-none cursor-pointer bg-none">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
            </svg>
        </button>
    @if($overrideMenu)
    <a href="{{route('home')}}">
        <x-elements.logo />
    </a>
    @else
        <x-elements.logo />
    @endif
    
    </div>

    <template x-if="menuOpen">
        <div x-transition:enter="transition transition-all ease-in ease-out duration-300"
            class="fixed top-0 bottom-0 left-0 right-0 z-50 justify-between bg-white backdrop-blur-sm w-96">
            <div class="relative flex items-start justify-between">
                <div class="flex flex-col">
                    @php
                        $insertedItemId = [];
                        if ($overrideMenu) {
                            $menu = $feMenu?->items->sortBy('order');
                        } else {
                            $menu = $this->record->items->sortBy('order');
                        }
                    @endphp

                    @if($menu)
                    <!-- Menu Items -->
                    @foreach ($menu as $item)
                        @if (in_array($item->id, $insertedItemId))
                            @continue
                        @endif
                        @php
                            $insertedItemId[] = $item->id;
                        @endphp

                        @if ($item->parent_id == null && !$item->has_submenu)
                            <x-cms-custom-navbar.simple-button :item="$item" />
                        @elseif($item->parent_id == null && $item->has_submenu)
                            <x-cms-custom-navbar.dropdown-button :item="$item" />
                        @else
                        @endif
                    @endforeach
                    @endif
                </div>
                <button @click="menuOpen = false" class="mt-2 mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </template>
</nav>

<nav class="hidden md:block">
    <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
        @if($overrideMenu)
        <a href="{{route('home')}}">
            <x-elements.logo />
        </a>
        @else
            <x-elements.logo />
        @endif
        <div class="flex justify-between h-16">
            {{-- <div class="flex items-center flex-shrink-0">
                    <!-- Your logo/image -->
                    <img class="block w-auto h-8 lg:hidden"
                        src="https://tailwindui.com/img/logos/workflow-mark-indigo-500.svg" alt="Workflow">
                    <img class="hidden w-auto h-8 lg:block"
                        src="https://tailwindui.com/img/logos/workflow-logo-indigo-500-mark-white-text.svg"
                        alt="Workflow">
                </div> --}}
            <div class="sm:-my-px sm:ml-6 sm:flex sm:space-x-8">
                @php
                    $insertedItemId = [];
                    if ($overrideMenu) {
                        $menu = $feMenu?->items->sortBy('order');
                    } else {
                        $menu = $this->record->items->sortBy('order');
                    }
                @endphp

                @if($menu)
                @foreach ($menu as $item)
                    @if (in_array($item->id, $insertedItemId))
                        @continue
                    @endif
                    @php
                        $insertedItemId[] = $item->id;
                    @endphp
                    @if ($item->parent_id == null && !$item->has_submenu)
                        <x-cms-custom-navbar.simple-button :item="$item" />
                    @elseif($item->parent_id == null && $item->has_submenu)
                        <x-cms-custom-navbar.dropdown-button :item="$item" />
                    @else
                    @endif
                @endforeach
                @endif
            </div>
            <x-cms-custom-navbar.search-menu-input :searchKey="$searchKey"/>
        </div>
        
    </div>
</nav>
