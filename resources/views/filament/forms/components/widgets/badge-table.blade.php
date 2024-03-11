<div class="flex justify-center m-1">
    @if (isset($getRecord()->json_content['img']))
        <img width="202" height="62" class="rounded border border-slate-300" src="{{ Storage::url($getRecord()->json_content['img']) }}"
            alt="{{ $getRecord()->json_content['alt'] }}">
    @else
        <img style="max-height: 62px; object-fit:contain; background-color: #CCCCCC" width="202" height="62" class="rounded border border-slate-300" src="{{asset('placeholder-img.jpg')}}"
            alt="{{ $getRecord()->json_content['alt'] }}">
    @endif
</div>
