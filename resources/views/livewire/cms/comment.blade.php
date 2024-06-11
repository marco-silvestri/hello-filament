<div class=pl-2>
    @if ($postId)
        <div>
            <h4 class="uppercase comment--text">
                {{ __('comments.lbl-leave-comment') }}
            </h4>
            <form wire:submit='sendComment'>
                <x-honeypot />
                <input class="w-full border-2 border-display-100" type="text" wire:model='newComment' />
                <input type="hidden" wire:model='parentId' />
                <button class="my-4 button__brand" type="submit" @click="replySent = true">{{ __('comments.btn-send') }}</button>
                <div class="text-[10px] text-brand-300 font-brand-alt">@error('newComment') {{ $message }} @enderror</div>
                <div class="text-[10px] text-brand-300 font-brand-alt">@if(session('spamDetection')) {{session('spamDetection')}} @enderror</div>
                <div class="text-[10px] text-green-300 font-brand-alt">@if(session('commentSuccess')) {{session('commentSuccess')}} @enderror</div>
                <div class="text-[10px] text-brand-300 font-brand-alt">@if(session('commentFailure')) {{session('commentFailure')}} @enderror</div>
            </form>
        </div>
    @else
    <div x-data="{
        isOpen: false,
        replySent: false,
        showReply() {
            this.isOpen = !this.isOpen;
            $wire.newComment = '';
            $wire.parentId = {{ $comment->id }};
        },
    }">
        <p>
            <span class="font-bold comment--text">
                {{ $comment->author ? $comment->author->name : __('comments.lbl-anonymous') }}
            </span>
            <span class="italic comment--text">
                {{ Carbon\Carbon::parse($comment->created_at)->diffForHumans() }}
            </span>
            <button class="pl-2 uppercase comment--text hover:text-display-400" @click="showReply()">
                {{ __('comments.btn-reply') }}
            </button>

            {{-- {{ __('comments.lbl-writes') }}
            {{ __('comments.lbl-on') }} {{ Carbon\Carbon::parse($comment->created_at)->format('d/m/Y') }}
            {{ __('comments.lbl-at') }} {{ Carbon\Carbon::parse($comment->created_at)->format('H:i') }}: --}}
        </p>
        <p class="italic comment--text">
            {!! $comment->body !!}
        </p>
            <div x-show="!replySent">
                <form x-show="isOpen" wire:submit='sendComment'>
                    <x-honeypot />
                    <input class="border-display-50" type="text" wire:model='newComment' />
                    <input type="hidden" wire:model='parentId' />
                    <button type="submit" @click="replySent = true">{{ __('comments.btn-send') }}</button>
                    <div>@error('newComment') {{ $message }} @enderror</div>
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
                    <div class="pl-4">
                        <livewire:cms.comment wire:key="{{ md5($reply->body) }}" wire:ignore :comment='$reply'>
                    </div>
                @endforeach
            @endif
        </div>
    @endif
</div>
