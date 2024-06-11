@unless ($breadcrumbs->isEmpty())
    <nav class="container mx-auto">
        <ol class="flex flex-wrap py-4 rounded breadcrumb--text">
            @foreach ($breadcrumbs as $breadcrumb)

                @if ($breadcrumb->url && !$loop->last)
                    <li>
                        <a href="{{ $breadcrumb->url }}" class="breadcrumb--text breadcrumb--text__link">
                            {{ $breadcrumb->title }}
                        </a>
                    </li>
                @else
                    <li>
                        {{ $breadcrumb->title }}
                    </li>
                @endif

                @unless($loop->last)
                    <li class="px-2 breadcrumb--text">
                        >
                    </li>
                @endif

            @endforeach
        </ol>
    </nav>
@endunless
