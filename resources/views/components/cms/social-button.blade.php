<div class="p-8 mb-8 md:pr-4 flex flex-row footer-info">
    <div class="mr-4">
        <p>{{ __('common.lbl-social-button')}}</p>
    </div>
    <div class="flex justify-between w-40  pl-0 space-x-4">
    
        @foreach ($socialButtons as $socialButton)
            <a href="{{ $socialButton['href'] }}" target="_blank" class="w-8 h-8" data-bs-toggle="tooltip"
                title="{{__('common.lbl-social-button')}} {{ $socialButton['name'] }}" aria-label="{{__('common.lbl-social-button')}} {{ $socialButton['name'] }}">
                <x-dynamic-component :component="$socialButton['icon']" class="w-4 h-4" />
            </a>
        @endforeach
    </div>
</div>
