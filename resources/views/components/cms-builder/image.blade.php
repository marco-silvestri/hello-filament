<div class="aspect-video w-64 flex justify-center mb-2">
    <x-curator-glider
        class="object-cover w-auto"
        :media="$block['data']['image']"
        width="{{$block['data']['width'] ?? 500}}"
        height="{{$block['data']['height'] ?? 'auto'}}"
    />
</div>