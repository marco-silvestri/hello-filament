<div class=pl-2>
    @if ($postId)
        <div>
            <form wire:submit='sendComment'>
                <x-honeypot />
                <input type="text" wire:model='newComment' />
                <input type="hidden" wire:model='parentId' />
                <button type="submit" @click="replySent = true">{{ __('comments.btn-send') }}</button>
            </form>
        </div>
    @else
        <p>{{ $comment->author ? $comment->author->name : __('comments.lbl-anonymous') }}
            {{ __('comments.lbl-writes') }}
            {{ __('comments.lbl-on') }} {{ Carbon\Carbon::parse($comment->created_at)->format('d/m/Y') }}
            {{ __('comments.lbl-at') }} {{ Carbon\Carbon::parse($comment->created_at)->format('H:i') }}:</p>
        <p>{!! $comment->body !!}</p>

        <div x-data="{
            isOpen: false,
            replySent: false,
            showReply() {
                this.isOpen = !this.isOpen;
                $wire.newComment = '';
                $wire.parentId = {{ $comment->id }};
            },
        }">
            <div x-show="!replySent">
                <button @click="showReply()">{{ __('comments.btn-reply') }}</button>
                <form x-show="isOpen" wire:submit='sendComment'>
                    <x-honeypot />
                    <input type="text" wire:model='newComment' />
                    <input type="hidden" wire:model='parentId' />
                    <button type="submit" @click="replySent = true">{{ __('comments.btn-send') }}</button>
                </form>
            </div>
            <div x-show="replySent">
                <div>
                    {{ __('comments.lbl-reply-sent') }}
                </div>
            </div>
        </div>

        <div wire:ignore>
            @if ($comment->replies)
                @foreach ($comment->replies as $reply)
                    {{ $loop->iteration }}
                    <div class="pl-4">
                        <livewire:cms.comment wire:key="{{ md5($reply->body) }}" wire:ignore :comment='$reply'>
                    </div>
                @endforeach
            @endif
        </div>
    @endif
</div>
