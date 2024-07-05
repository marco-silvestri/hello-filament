@props([
    'data' => [],
])

<div>
    <label for="{{$data['name']}}">
        {{$data['label']}}
    </label>
    <input type="{{$data['type']}}" name="{{$data['name']}}"
    wire:key="{{$this->getInputKey($data['name'])}}"
    placeholder="{{$data['label']}}"
    wire:model="filledData.{{$data['id']}}"/>
</div>
