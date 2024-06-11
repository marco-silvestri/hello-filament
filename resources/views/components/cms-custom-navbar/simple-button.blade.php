@props(['item'])
@if($item->getNavigationSlug())
<a href="{{$item->getNavigationSlug()}}"
    class="inline-flex items-center menu-item">{{ $item->name }}</a>
@else
<span
    class="inline-flex items-center menu-item">{{ $item->name }}</span>
@endif
