@props([
    'size' => 'base',
    'title' => null,
    'slug' => null,
    'groupType' => null,
    'altTextColorClass' => null
])

<div class="mb-2">
    @if($slug)
    <a href="{{route('')}}" class="group--title group--title__base @if($altTextColorClass) {{$altTextColorClass}} @endif">
        {{$title}}
    </a>
    @else
    <span class="group--title group--title__base @if($altTextColorClass) {{$altTextColorClass}} @endif">
        {{$title}}
    </span>
    @endif
    <x-elements.small-hr/>
</div>
