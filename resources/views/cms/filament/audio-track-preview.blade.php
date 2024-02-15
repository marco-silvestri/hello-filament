{{-- @php
    $audio = App\Models\Audio::find($getState())->first();
@endphp --}}
@if ($getState() != null)
    <audio controls>
        <source src="{{ Storage::url($getState()['path']) }}" type="audio/mpeg">
    </audio>
@endif
