@props(['item'])

<div class="flex items-center">
    <!-- Dropdown menu -->
    <div x-data="{ open: false, subMenuOpen: false }" class="relative ml-3">
        <button @click="open = !open" type="button"
            class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-300 hover:text-white hover:border-white focus:outline-none focus:text-white focus:border-white">
            {{ $item->name }}
            <svg :class="{ 'transform rotate-180': open }"
                class="ml-2 h-5 w-5 text-gray-400 group-hover:text-gray-500 transition-transform duration-200 "
                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd"
                    d="M6.293 7.293a1 1 0 011.414 0L10 9.586l2.293-2.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                    clip-rule="evenodd" />
            </svg>
        </button>
        <!-- Dropdown menu panel, show/hide based on dropdown state -->
        <div x-show="open" @click.away="open = false"
            class="origin-top absolute left-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 divide-y divide-gray-100 focus:outline-none"
            role="menu" aria-orientation="vertical" aria-labelledby="options-menu">
            <div class="py-1" role="none">
                <!-- Dropdown item -->
                @foreach ($item->childrens as $children)
                    @php
                        $insertedItemId[] = $children->id;
                    @endphp
                    @if ($children->has_submenu)
                        <x-cms-custom-navbar.submenu-dropdown-item :item="$children" />
                    @else
                        <x-cms-custom-navbar.simple-submenu-item :item="$children" />
                    @endif
                @endforeach
                <!-- More dropdown items... -->
            </div>
        </div>
    </div>
</div>
