@props([
    'size' => 'base',
    'title' => null,
    'anchor' => null,
])

<div>
    <h2 class="strip--title
    @if($size === 'base')
        strip--title__base
    @else
        strip--title__small
    @endif
        ">
    @if($anchor)
        <a href="{{$anchor}}">{{$title}}</a>
    @else
        {{$title}}
    @endif
    </h2>
    <x-elements.hr/>
</div>
