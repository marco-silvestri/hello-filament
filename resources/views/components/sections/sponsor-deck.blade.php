<section class="w-full ">
    <div class="space-between">
        <x-elements.group-title :title="__('home-page.sponsor-title')" />
    </div>
    <div class="flex flex-col">
        @foreach (\App\Models\WdgSponsor::getAll() as $sponsor)
            <div class="my-4">
                <img class="w-full" src="{{ Storage::disk('public')->url($sponsor->src) }}" alt="{{ $sponsor->alt }}" />
            </div>
        @endforeach
    </div>
</section>
