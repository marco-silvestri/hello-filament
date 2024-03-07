<div class="mb-2">
    <h2>Widget preview</h2>
</div>

<div class="flex justify-center">
    <img class="rounded border border-slate-300" src="{{ Storage::url($this->record->json_content['immagine']) }}" alt="{{$this->record->json_content['alt']}}">
</div>
