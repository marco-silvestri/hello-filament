<div class="post--meta-info">
    {{__('common.lbl-written-by')}}
    @if($post->author->slug)
    <a class="font-bold" href="{{route('author', ['slug' => $post->author->slug->name])}}">
    @else
    <a class="font-bold" href="{{route('home')}}">
    @endif
        {{$post->author->name}}
    </a>
    - {{\Carbon\Carbon::parse($post->published_at)->translatedFormat('d F Y')}}
</div>
