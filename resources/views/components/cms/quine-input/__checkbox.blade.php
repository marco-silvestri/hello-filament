@props([
    'data' => [],
    'additionalLabel' => null,
])

<div>
    @if($additionalLabel)
    {!! $additionalLabel['label'] !!}
    <label for="{{$data['name']}}">
        Si, acconsento
    </label>
    @else
    <label for="{{$data['name']}}">
        {{$data['label']}}
    </label>
    @endif

    <input type="{{$data['type']}}" name="{{$data['name']}}"
    wire:key="{{$this->getInputKey($data['name'])}}"
    placeholder="{{$data['label']}}"
    wire:model="filledData.{{$data['id']}}"/>
</div>
