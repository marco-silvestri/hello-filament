@props([
    'size' => 'base',
    'title' => null,
    'slug' => null,
    'groupType' => null,
    'altTextColorClass' => null,
    'routeName' => null,
])

<div class="mb-2">
    @if($routeName && $slug)
    <a href="{{route($routeName, ['slug'=>$slug])}}" class="group--title group--title__base @if($altTextColorClass) {{$altTextColorClass}} @endif">
        {{$title}}
    </a>
    @else
    <span class="group--title group--title__base @if($altTextColorClass) {{$altTextColorClass}} @endif">
        {{$title}}
    </span>
    @endif
    <x-elements.small-hr/>
</div>
