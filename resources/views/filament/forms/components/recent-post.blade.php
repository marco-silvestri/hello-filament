<div class="relative flex rounded-md">
    <div class="flex">
        <div class="px-2 py-3">
            <div class="w-10 h-10">
                <img src="{{ $src }}" role="img" class="object-cover w-full h-full overflow-hidden rounded-full shadow" />
            </div>
        </div>

        <div class="flex flex-col justify-center py-2 pl-3">
            <p class="pb-1 text-sm font-bold">{{ $title }}</p>
            <div class="flex flex-col items-start">
                <p class="text-xs leading-5">{{ $excerpt }}</p>
            </div>
        </div>
    </div>
</div>
