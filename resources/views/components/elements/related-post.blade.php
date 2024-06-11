<a class='w-16 h-32' href='{$post->slug}'>
    <x-curator-glider class='w-full h-[392px] object-cover rounded-md' :media='$post->featuredImage?->id' fit='crop-center'
    format='webp' fallback='article_fallback' />
</a>
