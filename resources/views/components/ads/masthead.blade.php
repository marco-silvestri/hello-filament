@props([
    'n' => 'false',
])
@if(config('cms.google_ads_key'))
    <div class="px-4 mx-auto my-8 max-w-7xl sm:px-6 lg:px-8">
        <img class="mx-auto" src="https://fakeimg.pl/970x250/cccccc/ab8585?text=Masthead+{{$n}}+970x250&font=bebas">
    </div>
    @endif