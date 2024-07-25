@props([
    'n' => 'false',
    'x'=>'300',
    'y'=>'250',
])
@if(config('cms.google_ads_key'))
    <div class="my-4">
        <img class="mx-auto" src="https://fakeimg.pl/{{$x}}x{{$y}}/cccccc/ab8585?text=Box+{{$n}}+{{$x}}x{{$y}}&font=bebas">
    </div>
    @endif