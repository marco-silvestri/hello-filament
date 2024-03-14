@props(['item'])

<div class="py-1 relative" role="none">
    <!-- Dropdown item -->
    <button @click="subMenuOpen = !subMenuOpen" type="button"
        class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900 focus:outline-none"
        role="menuitem">
        {{ $item->name }}
        <svg :class="{ 'transform rotate-180': subMenuOpen }"
            class="ml-auto h-5 w-5 text-gray-400 group-hover:text-gray-500 transition-transform duration-200"
            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path fill-rule="evenodd"
                d="M6.293 7.293a1 1 0 011.414 0L10 9.586l2.293-2.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                clip-rule="evenodd" />
        </svg>
    </button>
    <div style="left: 228px;" x-show="subMenuOpen" @click.away="subMenuOpen = false"
        class="absolute mt-1 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 divide-y divide-gray-100 focus:outline-none"
        role="menu" aria-orientation="vertical" aria-labelledby="options-menu">
        <div class="py-1" role="none">
            @foreach ($item->childrens as $children)
                @if ($children->has_submenu)
                    <x-cms-custom-navbar.submenu-dropdown-item :item="$children" />
                @else
                    <x-cms-custom-navbar.simple-submenu-item :item="$children" />
                @endif
            @endforeach
        </div>
    </div>
</div>
