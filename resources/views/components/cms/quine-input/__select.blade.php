@props([
    'data' => [],
])

<div>
    <label for="{{$data['name']}}">
        {{$data['label']}}
    </label>
    <select
        wire:model="filledData.{{$data['id']}}"
        wire:key="{{$this->getInputKey($data['name'])}}">
        @foreach ($data['options'] as $option )
            <option value="{{$option['id']}}">
                {{$option['name']}}
            </option>
        @endforeach
    </select>
</div>
