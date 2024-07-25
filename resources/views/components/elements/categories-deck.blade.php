@props([
    'categories' => []
])

@if(count($categories) > 0)
<div class="flex">
    @foreach ($categories as $category)
    @if (!$loop->first)
        <span class="mx-2 font-extrabold">|</span>
    @endif
    <x-elements.group-title :title="$category->name"/>
    @endforeach
</div>
@else
<div class="flex space-x-4">
    <x-elements.group-title :title="__('posts.lbl-uncategorized')" />
</div>
@endif
