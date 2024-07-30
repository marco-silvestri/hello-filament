@if (\App\Models\WdgSponsor::getAll()->count()>0)
<section class="w-full ">
    <div class="space-between">
        <x-elements.group-title :title="__('home-page.sponsor-title')" />
    </div>
    <div class="flex flex-col">
        @foreach (\App\Models\WdgSponsor::getAll() as $sponsor)
            <div class="my-4">
                <a href="{{$sponsor->json_content['href']}}" target="_blank">
            <img class="w-[200px] h-[60px]" src="{{ Storage::disk('public')->url($sponsor->json_content['img']) }}" alt="{{ $sponsor->json_content['alt'] }}" />
            </a>
            </div>
        @endforeach
    </div>
</section>
@endif
