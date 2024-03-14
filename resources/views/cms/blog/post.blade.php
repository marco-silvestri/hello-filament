<x-layouts.post>
    @dump($post)
    <div>
        <livewire:cms.comment wire:ignore :postId="$post->id" />
    </div>
    <div wire:ignore>
        @foreach ($post->comments as $comment)
            <div>
                <livewire:cms.comment wire:ignore wire:key="{{md5($comment->body)}}" :$comment />
            </div>
        @endforeach
    </div>
</x-layouts.post>
