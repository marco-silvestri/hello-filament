<div class="flex justify-between w-40 p-4 pl-0 space-x-4">
    @foreach ($shareButtons as $shareButton)
        <a href="{{ $shareButton['href'] }}"
            target="_blank"
            class="w-8 h-8"
            data-bs-toggle="tooltip"
            title="Condividi su {{ $shareButton['name'] }}"
            aria-label="Condividi su {{ $shareButton['name'] }}">
            <x-dynamic-component :component="$shareButton['icon']"
                class="w-8 h-8"
                style="color: {{ $shareButton['color'] }};"/>
        </a>
    @endforeach
</div>
