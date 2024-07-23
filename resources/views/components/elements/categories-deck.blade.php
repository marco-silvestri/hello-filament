@props([
    'categories' => []
])

@if(count($categories) > 0)
<div >
    @foreach ($categories as $category)
    <x-elements.group-title :title="$category->name" />
    @endforeach
</div>
@else
<div class="flex space-x-4">
    <x-elements.group-title :title="__('posts.lbl-uncategorized')" />
</div>
@endif
