@if(count($mostRead) > 0)
<div class="my-4">
    <x-elements.strip-title :title="__('posts.lbl-most-read')"/>
    @foreach ($mostRead as $post)
        <x-cards.highlight-sm-card :post="$post" :group="$post->categories->first()->name" shownNumber="{{$loop->iteration}}"/>
    @endforeach
</div>
@endif
