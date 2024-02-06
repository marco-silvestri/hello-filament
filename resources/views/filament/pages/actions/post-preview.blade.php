@php
    $relatedPosts = [];
@endphp
<div class="flex flex-col">
    @forelse ($record->json_content as $block)
        @if ($block['type'] == 'heading')
            @include('components.builder.heading')
        @elseif($block['type'] == 'paragraph')
            @include('components.builder.paragraph')
        @elseif($block['type'] == 'image')
            @include('components.builder.image')
        @elseif($block['type'] == 'related_posts')
            @php
                $relatedPostsId = $block['data']['related_posts'];
                $relatedPost = \App\Models\Post::query()
                    ->whereIn('id', $relatedPostsId)
                    ->get();
            @endphp
        @elseif($block['type'] == 'video')
            @include('components.builder.video')
        @else
        @endif
    @empty
    @endforelse
</div>
@if (count($relatedPostsId) > 0)
    <hr>
    <div class="flex overflow-x-auto" id="card-slider">
        @foreach ($relatedPost as $post)
            <div class="max-w-sm rounded overflow-hidden shadow-lg p-5">
                <x-curator-glider class="object-cover w-auto" :media="$post->feature_media_id" fallback="card_fallback" width="500" />
                <div class="px-6 py-4">
                    <div class="font-bold text-xl mb-2">The Coldest Sunset</div>
                    <p class="text-gray-700 text-base">
                        {{ Str::limit($post->excerpt, 100, '...') }}
                    </p>
                </div>
            </div>
        @endforeach
    </div>
@endif
