@props([
    'size' => 'base',
    'title' => null
])

<div>
    <h2 class="strip--title
    @if($size === 'base')
        strip--title__base
    @else
        strip--title__small
    @endif
        ">
        {{$title}}
    </h2>
    <x-elements.hr/>
</div>
