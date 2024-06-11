@if ($media)
    @if (str($media->type)->contains('image'))
    <figure class="{{ $figclass }}">
        <img
            src="{{ $source }}"
            alt="{{ $media->alt ?? $alt }}"
            @if ($width && $height)
                width="{{ $width }}"
                height="{{ $height }}"
            @else
                width="{{ $media->width }}"
                height="{{ $media->height }}"
            @endif
            @if ($sourceSet)
                srcset="{{ $sourceSet }}"
                sizes="{{ $sizes }}"
            @endif
            {{ $attributes->filter(fn ($attr) => $attr !== '') }}
        />

    @if($caption != null )
        <figcaption class="text-[12px] font-brand text-display-500 italic">{{ $caption }}</figcaption>
    @elseif($media->caption != null)
        <figcaption>{{ $caption }}</figcaption>
    @endif
    </figure>
    @else
        <x-curator::document-image
            label="{{ $media->name }}"
            icon-size="xl"
            {{ $attributes->merge(['class' => 'p-4']) }}
        />
    @endif
@endif
