@props([
    'categories' => []
])

@if(count($categories) > 0)
<div class="flex space-x-4">
    @foreach ($categories as $category)
    <x-elements.group-title :title="$category->name" />
    @endforeach
</div>
@endif
