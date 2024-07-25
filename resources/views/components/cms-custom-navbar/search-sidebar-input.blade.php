@props(['searchKey'])
<form class="items-center max-w-lg bg-shade-500 p-4 rounded-lg mt-6" action='{{ route("search") }}' method="GET" autocomplete="off">  
    <span class="group--title group--title__base text-brand-500 font-bold">
    {{__('search.lbl-search')}}
    </span>
    <div class="relative w-full">
       
        <input type="text" id="k" name="k" value="{{$searchKey}}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm  focus:ring-brand-500 focus:border-brand-500 block w-full  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-brand-500 dark:focus:border-brand-500" placeholder="" required />
        <button type="submit" class="absolute inset-y-0 end-0 flex items-center pe-3">
        <svg class="w-4 h-4 me-2 text-brand-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
        </svg>
        </button>
        
        
    </div>
    <div class="relative w-full text-center mt-4">
    <button type="submit" class="inline-flex items-center py-2 px-6 text-sm font-small text-white bg-brand-500  border border-brand-500 hover:bg-brand-500 focus:ring-4 focus:outline-none focus:ring-blue-300">
    {{__('search.plh-search')}}
    </button>
    </div>
</form>
