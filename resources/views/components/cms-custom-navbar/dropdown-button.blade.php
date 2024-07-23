@props(['item'])

<div x-cloak x-data="{ open: false, subMenuOpen: false }" class="relative inline-flex items-center">
    <!-- Dropdown menu -->
        <button @click="open = !open" type="button"
            class="inline-flex items-center menu-item">
            {{ $item->name }}
            <svg :class="{ 'transform rotate-180': open }"
                class="w-5 h-5 ml-2 transition-transform duration-200 text-display-950 group-hover:text-display-200"
                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd"
                    d="M6.293 7.293a1 1 0 011.414 0L10 9.586l2.293-2.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                    clip-rule="evenodd" />
            </svg>
        </button>
        <!-- Dropdown menu panel, show/hide based on dropdown state -->
        <div x-show="open" @click="open = true" @click.away="open=false"
            class="absolute left-0 w-56 p-1 mt-12 origin-top bg-white divide-y rounded-md shadow-lg divide-display-200 ring-1 ring-black ring-opacity-5 focus:outline-none"
            role="menu" aria-orientation="vertical" aria-labelledby="options-menu">
            <div class="flex flex-col space-y-2" role="none">
                <!-- Dropdown item -->
                @foreach ($item->childrens as $children)
                    @php
                        $insertedItemId[] = $children->id;
                    @endphp
                    @if ($children->has_submenu)
                        <x-cms-custom-navbar.dropdown-button :item="$children" />
                    @else
                        <x-cms-custom-navbar.simple-button :item="$children" />
                    @endif
                @endforeach
                <!-- More dropdown items... -->
            </div>
        </div>
</div>
