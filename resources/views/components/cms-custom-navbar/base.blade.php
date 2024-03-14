<div x-data="{ open: false }" class="bg-gray-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                {{-- <div class="flex-shrink-0 flex items-center">
                    <!-- Your logo/image -->
                    <img class="block lg:hidden h-8 w-auto"
                        src="https://tailwindui.com/img/logos/workflow-mark-indigo-500.svg" alt="Workflow">
                    <img class="hidden lg:block h-8 w-auto"
                        src="https://tailwindui.com/img/logos/workflow-logo-indigo-500-mark-white-text.svg"
                        alt="Workflow">
                </div> --}}
                <div class="hidden sm:-my-px sm:ml-6 sm:flex sm:space-x-8">
                    @php
                        $insertedItemId = [];
                    @endphp
                    @foreach ($this->record->items->sortBy('order') as $item)
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

                </div>
            </div>

        </div>
    </div>
</div>
