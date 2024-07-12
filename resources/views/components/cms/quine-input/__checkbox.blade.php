@props([
    'data' => [],
    'additionalLabel' => null,
])

<div class="flex-col items-center justify-center text-xs form__checkbox font-brand">
    @if($additionalLabel)
    <p class="mb-4">
        {!! $additionalLabel['label'] !!}
    </p>

    <label class="mt-2" for="{{$data['name']}}">
        Si, acconsento
    </label>
    @else
    <label class="mt-2" for="{{$data['name']}}">
        {{$data['label']}}
    </label>
    @endif

    <input type="{{$data['type']}}" name="{{$data['name']}}"
    wire:key="{{$this->getInputKey($data['name'])}}"
    placeholder="{{$data['label']}}"
    wire:model="filledData.{{$data['id']}}"/>
</div>
