<x-layouts.public>
    @section('meta-tags')
        <meta name="og:title" content="{{ $post->og_title ?? $post->title }}">
        <meta name="og:description" content="{{ $post->og_description ?? $post->excerpt }}">
        <meta property="og:type" content="article">
        <meta property="og:url" content="{{ config('app.url') . '/' . $post->slug->name }}">
        <meta property="og:site_name" content="{{ __('meta-tags.title') }}">
        <meta property="og:image" content="{{ $post->featuredImage?->url ?? '' }}">
        <meta property="article:published_time" content="{{ $post->published_at ?? $post->created_at }}">
        <meta property="article:modified_time" content="{{ $post->updated_at }}">
        <meta property="article:section" content="{{ $post->categories?->first()->name ?? '' }}">
        <meta property="article:tag" content="{{ $post->tags?->first()->name ?? '' }}">

        <script type="application/ld+json">
        {
            "@context":"https:\/\/schema.org",
            "@type":"Article",
            "headline":"{{$post->og_title ?? $post->title}}",
            "name":"{{$post->og_title}}",
            "description":"{{$post->og_description ?? $post->excerpt}}",
            "datePublished":"{{$post->published_at ?? $post->created_at}}",
            "author":"{{$post->author->name}}",
            "image":"{{$post->featuredImage?->url ?? ""}}",
            "publisher":{
                "@type":"Organization",
                "name":"{{config('app.name')}}",
                "logo":{
                    "@type":"ImageObject",
                    "url":""
                }
            }
        }
        </script>
    @endsection

    @section('title')
        <title>{{ $post->title }}</title>
        <meta name="description" content="{{ $post->excerpt }}">
        <link rel="canonical" href="{{ config('app.url') . '/' . $post->slug->name }}">
    @endsection

    @isset($isPreview)
        <div class="sticky top-0 z-50 w-full py-2 text-center text-white opacity-80 bg-brand-500">
            {{ __('posts.lbl-preview') }}
        </div>
    @endisset
    <x-elements.blog-container>
        @section('menu')
            @if ($menu)
                <x-cms-custom-navbar.base :feMenu="$menu" :overrideMenu="true" />
            @endif
        @endsection

        @section('deck')
            @if (config('cms.layout.has_breadcrumbs'))
                {{ Breadcrumbs::render('post', $post) }}
            @endif
            <div class="flex flex-col justify-between md:flex-row">
                <div class="w-4/4 md:w-3/4 md:pr-4 px-4 mb-4 @if ($post->settings?->isSponsored) bg-brand-50 @endif relative">
                    @if ($post->settings?->isSponsored)
                        <div class="absolute top-0 right-0 px-4 py-2 bg-brand-500">
                            <p class="text-white uppercase font-brand">
                                {{ $post->settings->sponsor->name }}
                            </p>
                        </div>
                    @endif
                    <x-elements.categories-deck :categories="$post->categories" />
                    <div class="mt-2 mb-2">
                        <h1 class="mt-4 mb-6 post--title post--title__base">
                            {{ $post->title }}
                        </h1>
                        <x-elements.post-meta-info :post="$post" />

                    </div>

                    <x-curator-glider class="w-full h-[392px] object-cover rounded-md" :media="$post->featuredImage?->id" fit="crop-center"
                        format="webp" fallback="article_fallback" />
                    <div id="post-content" class="font-brand">
                        @if ($post->json_content)
                            @foreach ($post->json_content as $dataBlock)
                                {!! \App\Services\BlockLoader::renderDataBlock($dataBlock) !!}
                            @endforeach
                        @endif
                    </div>
                    <div class="flex items-center mb-8 space-x-4">
                        @if ($post->tags)
                            @foreach ($post->tags as $tag)
                                @if ($tag->slug)
                                    <a href="{{ route('tag', ['slug' => $tag->slug->name]) }}"
                                        class="text-[10px] text-center button__brand--inverted">
                                        {{ $tag->name }}
                                    </a>
                                @endif
                            @endforeach
                        @endif
                    </div>
                    <x-cms.social-sharing :post="$post" />
                    @if (isset($prevPost) && isset($nextPost))
                    <div class="flex items-center justify-between my-16">
                        @if ($prevPost)
                            <a href="{{ $prevPost->slug->name }}" class="flex items-start w-1/2">
                                <div class="mr-4">
                                    <x-curator-glider class="object-cover rounded-full h-[85px] w-[85px]" :media="$prevPost->featuredImage?->id"
                                        fit="crop-center" format="webp" width="170" height="170"
                                        fallback="article_fallback" />
                                </div>
                                <div class="flex flex-col">
                                    <div class="flex space-x-2">
                                        <x-elements.left-arrow />
                                        <span class="text-display-500 text-[12px] leading-4 font-brand-alt">
                                            {{ __('posts.lbl-prev-post') }} </span>
                                    </div>
                                    <div class="font-brand font-bold text-[14px] tracking-[0.7px] leading-[16px]">
                                        {{ $prevPost->title }}</div>
                                </div>
                            </a>
                        @endif
                        @if ($nextPost)
                            <a href="{{ $nextPost->slug->name }}" class="flex items-start w-1/2 justify-end">
                                <div class="flex flex-col ml-2">
                                    <div class="flex space-x-2">
                                        <span class="text-display-500 text-[12px] leading-4 font-brand-alt">
                                            {{ __('posts.lbl-next-post') }} </span>
                                        <x-elements.right-arrow />
                                    </div>
                                    <div class="font-brand font-bold text-[14px] tracking-[0.7px] leading-[16px]">
                                        {{ $nextPost->title }}</div>
                                </div>
                                <div class="ml-4">
                                    <x-curator-glider class="object-cover rounded-full h-[85px] w-[85px]" :media="$nextPost->featuredImage?->id"
                                        fit="crop-center" format="webp" width="170" height="170"
                                        fallback="article_fallback" />
                                </div>
                            </a>
                        @endif
                    </div>
            @endif
            @if (config('app.comments'))
                @if (!isset($isPreview))
                        <div class="flex flex-col justify-between px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                            <div class="mr-4">
                                <div
                                    class="font-brand font-bold text-[22px] uppercase tracking-[1.1px] leading-8
                    mb-4">
                                    @if ($post->commentsCount == 0)
                                        {{ __('comments.lbl-nocomments') }}
                                    @else
                                        {{ $post->commentsCount }}
                                        @if ($post->commentsCount > 1)
                                            {{ __('comments.lbl-comments') }}
                                        @else
                                            {{ __('comments.lbl-comment') }}
                                        @endif
                                    @endif
                                    <x-elements.small-hr />
                                </div>

                                <div wire:ignore class="mb-8 border border-b-2 border-transparent border-b-display-50">
                                    @foreach ($post->comments as $comment)
                                        <div class="mb-4">
                                            <livewire:cms.comment wire:ignore wire:key="{{ md5($comment->body) }}" :$comment />
                                        </div>
                                    @endforeach
                                </div>
                                <div>
                                    <livewire:cms.comment wire:ignore :postId="$post->id" />
                                </div>
                            </div>
                        </div>
                @endif
            @endif
                </div>
                <div class="w-full mx-2 md:w-1/4">
                    <div class="flex flex-col">
                        <x-ads.box :n="1" />
                    </div>
                    <div class="flex flex-col">
                        <x-sections.sponsor-deck />
                    </div>
                    <div class="flex flex-col">
                        <x-ads.box :n="2" />
                    </div>
                    <div class="flex flex-col">
                        <x-ads.box :n="3" />
                    </div>
                </div>
            </div>
        @endsection

        @isset($relatedPost)
            @section('related')
                <x-sections.related-deck :section="$relatedPosts" :title="__('posts.lbl-related-posts')" />
            @endsection
        @endisset
    </x-elements.blog-container>
</x-layouts.public>
