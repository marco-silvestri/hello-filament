<?=
/* Using an echo tag here so the `<? ... ?>` won't get parsed as short tags */
'
<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL ?>
<rss xmlns:content="http://purl.org/rss/1.0/modules/content/"
    xmlns:wfw="http://wellformedweb.org/CommentAPI/"
    xmlns:dc="http://purl.org/dc/elements/1.1/"
    xmlns:atom="http://www.w3.org/2005/Atom"
    xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
    xmlns:media="http://search.yahoo.com/mrss/"
    xmlns:slash="http://purl.org/rss/1.0/modules/slash/"
    version="2.0">
    <channel>
        <atom:link href="{{ url($meta['link']) }}" rel="self" type="application/rss+xml" />
        <title>{!! \Spatie\Feed\Helpers\Cdata::out($meta['title']) !!}</title>
        <link>{!! \Spatie\Feed\Helpers\Cdata::out(url($meta['link'])) !!}</link>
        @if (!empty($meta['image']))
            <image>
                <url>{{ $meta['image'] }}</url>
                <title>{!! \Spatie\Feed\Helpers\Cdata::out($meta['title']) !!}</title>
                <link>{!! \Spatie\Feed\Helpers\Cdata::out(url($meta['link'])) !!}</link>
            </image>
        @endif
        <description>{!! \Spatie\Feed\Helpers\Cdata::out($meta['description']) !!}</description>
        <language>{{ $meta['language'] }}</language>
        <pubDate>{{ $meta['updated'] }}</pubDate>

        @foreach ($items as $item)
            <item>
                <title>{!! \Spatie\Feed\Helpers\Cdata::out($item->title) !!}</title>
                <link>{{ url($item->link) }}</link>
                <description>{!! \Spatie\Feed\Helpers\Cdata::out($item->summary) !!}</description>
                <author>{!! \Spatie\Feed\Helpers\Cdata::out(
                    $item->authorName . (empty($item->authorEmail) ? '' : ' <' . $item->authorEmail . '>'),
                ) !!}</author>
                <guid>{{ url($item->id) }}</guid>
                <pubDate>{{ $item->timestamp() }}</pubDate>
                @foreach ($item->category as $category)
                    <category>{{ $category }}</category>
                @endforeach
            </item>
        @endforeach
    </channel>
</rss>
