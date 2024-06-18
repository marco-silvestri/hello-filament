@props(['searchKey'])
<form class="flex items-center max-w-lg" action='{{ route("search") }}' method="GET" autocomplete="off">  
    <label for="voice-search" class="sr-only">Search</label>
    <div class="relative w-full">
        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
        <svg class="w-4 h-4 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
        </svg>
        </div>
        <input type="text" id="k" name="k" value="{{$searchKey}}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm block w-full ps-10 p-2.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" placeholder="{{__('search.lbl-search')}}" required />
        
    </div>
    <button type="submit" class="bg-display-500 inline-flex items-center ms-2 px-3 py-2 text-sm text-white">
    <img src="{{ asset('img/manu.svg') }}" alt="left-arrow">
    </button>
</form>
