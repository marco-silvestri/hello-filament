<?php

namespace App\Console\Commands\v1;

use Exception;
use DOMDocument;
use App\Models\Post;
use App\Models\Audio;
use Illuminate\Support\Str;
use App\Traits\Cms\HasWpData;
use Illuminate\Console\Command;
use Awcodes\Curator\Models\Media;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class HtmlToJson extends Command
{
    use HasWpData;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:html-to-json {id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        libxml_use_internal_errors(false);
        $rawReviews = $this->collectWpJson('legacy-data/audio_fader_reviews.json', 'reviews');
        if($this->argument('id'))
        {
            $posts = Post::query()
                ->where('id', $this->argument('id'))
                ->get();
        }else{
            $posts = Post::get();
        }

        $bar = $this->output->createProgressBar(count($posts));
        $bar->start();
        foreach ($posts as $post) {
            try {
                $html = $post->content;
                $html = $this->sanitizeLegacyHtml($html);
                $dom = new DOMDocument();
                @$dom->loadHTML($html);
                $json = $this->element_to_obj($dom->documentElement);
                $payload = [];
                $relatedArticles = [];

                //Parse stuff at the root element
                if (isset($json['children'][0]['html'])) {
                    $explodedJsonHtml = explode("\r\n", $json['children'][0]['html']);
                    $html = $this->parseLegacyButtons($explodedJsonHtml, $html);
                    $parsedBlocks = $this->parseRelatedPostsBlock($explodedJsonHtml, $html);
                    $html = $parsedBlocks['html'];
                    $relatedArticles = $parsedBlocks['payload'];
                }

                //$explodedHtml = explode("\r\n", $html);
                $explodedHtml = explode("\n", $html);

                collect($explodedHtml)
                    ->filter(fn ($el) => $el != "")
                    ->map(function ($el) use (&$payload) {
                        if (Str::containsAll($el, ['<a href', '<img '])) {
                            $packedBlock = $this->parseImageBlock($el);
                            $payload[] = isset($packedBlock[0]) ? $packedBlock[0] : $packedBlock;
                        } else if (Str::contains($el, '[embed')) {
                            $payload[] = $this->parseVideoBlock($el);
                        } else if (Str::contains($el, '[audio')) {
                            $payload[] = $this->parseAudioBlock($el);
                        }   else if (Str::contains($el, '<iframe class="wp-embedded-content'))
                        {
                            $payload[] = $this->parseIframeRelatedBlock($el);
                        } else {
                            $paragraph = $this->parseParagraphBlock($el);

                            if($paragraph)
                            {
                                $payload[] = $paragraph;
                            }
                        }
                    });

                $payload[] = $this->reviewParser($post, $rawReviews);
                $payload[] = $relatedArticles;

                $payload = $this->payloadCleaner($payload);

                $post->update([
                    'json_content' => $payload,
                ]);
            } catch (Exception $e) {
                Log::error("Could not process post {$post->id}", ['reason' => $e]);
                $post->update([
                    'json_content' => [$this->parseParagraphBlock($post->content)],
                    'has_importer_problem' => true,
                ]);
            }
            $bar->advance();
        }
        $bar->finish();
    }

    public function payloadCleaner(array $payload):array
    {
        foreach($payload as $key => $el)
        {
            if($el === '')
            {
                unset($payload[$key]);
            }
        }

        return $payload;
    }

    public function sanitizeLegacyHtml(string $html):string
    {
        $strippables = ['[buttons]', '[/buttons]'];

        $sanitizedHtml = str_replace($strippables, "", $html);

        return $sanitizedHtml;
    }

    public function parseLegacyButtons(array $arrayHtml, string $html):string
    {
        $regex = '/href="([^"]+)"\s+name="([^"]+)"/';
        $classes = [
            "padding" => "7px 21px",
            "margin" => "0 4px 4px 4px",
            "display" => 'inline-block',
            "width" => "inherit",
            "text-transform" => "uppercase",
            "font-size" => "14px",
            "color" => "#fff",
            "background-color" => "#0FADED",
        ];

        $cssStyles = $this->arrayToCss($classes);
        $buttonsToUpdate = [];
        collect($arrayHtml)
            ->filter(fn ($el) => $el != "" && Str::contains($el, "[button"))
            ->map(function ($el) use($regex, $cssStyles, &$buttonsToUpdate){
                preg_match($regex, $el, $matches);
                $href = $this->forceToHttps($matches[1]);
                $name = $matches[2];
                $button = "<a style='{$cssStyles}' href='{$href}' target='_blank'>{$name}</a>";

                $buttonsToUpdate[] = [
                    'old' => $el,
                    'new' => $button,
                ];
            });

        if(count($buttonsToUpdate) > 0)
        {
            foreach($buttonsToUpdate as $buttonToUpdate)
            {
                $html = Str::replace($buttonToUpdate['old'], $buttonToUpdate['new'], $html);
            }
        }

        return $html;
    }

    public function reviewParser(Post $post, Collection $rawReviews):?array
    {
        if($post->legacy_id)
        {
            $payload = [];
            $postReview = $rawReviews->filter(
                fn($reviewElement) => $reviewElement->post_id === $post->legacy_id
            );

            if(count($postReview) === 0)
            {
                return;
            }

            $payload = [
                'data' => [
                    'parameters' => [],
                ],
                'type' => 'review',
            ];

            $scores = [];
            $features = [];
            $postReview->map(function($kv)
            use(&$payload, &$scores, &$features){
                if(Str::contains($kv->meta_key, 'bk_ct'))
                {
                    $features[$kv->meta_key] = $kv->meta_value;
                }

                if(Str::contains($kv->meta_key, 'bk_cs'))
                {
                    $scores[$kv->meta_key] = $kv->meta_value;
                }

                if($kv->meta_key === 'bk_review_box_position')
                {
                    $payload['data']['position'] = $kv->meta_value;
                }

                if($kv->meta_key === 'bk_final_score')
                {
                    $payload['data']['total_score'] = $kv->meta_value;
                }

                if($kv->meta_key === 'bk_summary')
                {
                    $payload['data']['summary'] = $kv->meta_value;
                }
            });

            foreach($features as $featureKey => $featureValue)
            {
                $scoreKey = Str::replace('t','s', $featureKey);
                $payload['data']['parameters'][] = [
                    'key' => $featureValue,
                    'value' => $scores[$scoreKey],
                ];
            }

            return $payload;
        }
    }

    public function arrayToCss(array $classes) :string
    {
        $jsonClasses = json_encode($classes);
        $stringOfClasses = Str::replace(['"', "{", "}"], "", $jsonClasses);
        $stringOfClasses = Str::replace(",", "; ", $stringOfClasses);
        return $stringOfClasses;
    }

    public function forceToHttps(string $url):string
    {
        if(Str::contains($url, "http://", true))
        {
            return str_replace("http://", "https://", $url);
        }

        return $url;
    }

    public function parseIframeRelatedBlock(string $el):array
    {
        $dom = new DOMDocument();
        $dom->loadHTML($el);
        $iframes = $dom->getElementsByTagName('iframe');

        $slug = "";
        foreach ($iframes as $iframe) {
            if ($iframe->hasAttribute('src')) {
                $srcValue = $iframe->getAttribute('src');
                $urlComponents = parse_url($srcValue);
                $path = $urlComponents['path'];
                $parts = explode('/', trim($path, '/'));
                $slug = $parts[count($parts) - 2];
            }
        }

        $post = Post::whereHas('slug', function ($query) use ($slug) {
            $query->where('name', $slug);
        })->first();

        if(!$post->isEmpty())
        {
            return [
                'data' => [
                    'related_posts' => $post->id,
                ],
                'type' => 'related_posts',
        ];
        }
    }

    public function parseRelatedPostsBlock(array $arrayHtml, string $html): array
    {
        $legacyRelatedPostsUrl = [];
        $postsId = [];
        collect($arrayHtml)
            ->filter(fn ($el) =>
                $el != ""
                && (Str::contains($el, 'audiofader.com') && !Str::contains($el, '[button')))
            ->map(function ($url) use (&$postsId, &$legacyRelatedPostsUrl) {
                $slug = Str::replace([
                    'http://www.audiofader.com/',
                    'https://www.audiofader.com/',
                    '/'
                ], '', trim($url));

                $legacyRelatedPostsUrl[] = $url;
                $post = Post::whereHas('slug', function ($query) use ($slug) {
                    $query->where('name', $slug);
                })->first();

                $postsId[] = $post->id;
            });

        $html = Str::replace($legacyRelatedPostsUrl, "", $html);
        $html = trim($html);

        return [
            'html' => $html,
            'payload' => [
                'data' => [
                    'related_posts' => $postsId,
                ],
                'type' => 'related_posts',
            ]
        ];
    }

    public function parseAudioBlock(string $el): array
    {
        $url = str_replace(['[audio]', '[/audio]', ']'], "", $el);
        $url = explode("=", $url);
        $url = end($url);
        $url = str_replace('"', "", $url);

        $urlToArray = explode("/", $url);
        $filenameAndExtension = end($urlToArray);
        $path = "audio/{$filenameAndExtension}";

        $filenameToArray = explode('.', $filenameAndExtension);

        if (!Storage::exists($path)) {
            $file = Http::get($url);//file_get_contents($url);
            Storage::disk('public')->put($path, $file);
        }

        $mime = Storage::disk('public')->mimeType($path);

        $name = $filenameToArray[0];
        $extension = end($filenameToArray);

        $audioObj = Audio::firstOrCreate([
            'title' => $name,
        ], [
            'disk' => 'public',
            'directory' => 'audio',
            'visibility' => 'public',
            'name' => $filenameAndExtension,
            'path' => $path,
            'type' => $mime,
            'ext' => $extension,
            'attributes' => "",
            'description' => "",
        ]);

        return [
            'data' => [
                'audio' => "{$audioObj->id}",
                'caption' => '',
            ],
            'type' => 'audio',
        ];
    }

    public function parseVideoBlock(string $el): array
    {
        $url = str_replace(['[embed]', '[/embed]'], "", $el);
        $url = trim($url);
        return [
            'data' => [
                'url' => $url,
            ],
            'type' => 'video'
        ];
    }

    public function parseImageBlock(string $el): ?array
    {
        try {
            $dom = new DOMDocument();
            $dom->loadHTML($el);
            $json = $this->element_to_obj($dom->documentElement);
            $imgEl = $json['children'][0]['children'][0]['children'][0];
            $caption = "";

            if(strpos($el,'<a ') > 0)
            {
                $headParagraph = $this->parseParagraphBlock(explode('<a ',$el)[0]);
            }

            if(strpos($el,'</a>') < mb_strlen($el) - 4)
            {
                $tailParagraph = $this->parseParagraphBlock(explode('</a>',$el)[1]);
            }

            //probably there is a caption
            if (!isset($imgEl['src'])) {
                foreach ($json['children'][0]['children'][0]['children'] as $elKey => $elContent) {
                    foreach ($elContent as $nodeKey => $nodeValue) {
                        if ($nodeValue === 'em') {
                            $caption = $elContent['html'];
                        }
                    }
                }

                $imgEl = $json['children'][0]['children'][0]['children'][0]['children'][0];
            }

            $imgUrl = $imgEl['src'];
            $imgAlt = $imgEl['alt'];
            $imgWidth = $imgEl['width'];
            $imgHeight = $imgEl['height'];

            //$uniqName = uniqid();
            $explodedUrl = explode('.', $imgUrl);
            $extension = end($explodedUrl);
            $explodedUrl = explode('/', $imgUrl);
            $title = end($explodedUrl);
            $path = "media/{$title}";

            if (!Storage::disk('public')->exists($path)) {
                $img = Http::get($imgUrl);
                Storage::disk('public')->put($path, $img);
            }

            $size = Storage::disk('public')->size($path);
            $mime = Storage::disk('public')->mimeType($path);

            $imgObj = Media::firstOrCreate([
                'title' => $title,
            ], [
                'disk' => 'public',
                'visibility' => 'public',
                'name' => $title,
                'path' => $path,
                'width' => $imgWidth,
                'height' => $imgHeight,
                'ext' => $extension,
                'title' => $title,
                'type' => $mime,
                'size' => $size,
            ]);


            $returnable = [];

            if(isset($headParagraph) && $headParagraph)
            {
                $returnable = $headParagraph;
            }

            $returnable[] = [
                'data' => [
                    'image' => $imgObj->id,
                    'alt' => $imgAlt,
                    'width' => $imgWidth,
                    'height' => $imgHeight,
                    'caption' => $caption
                ],
                'type' => 'image',
            ];

            if(isset($tailParagraph) && $tailParagraph)
            {
                $returnable[] = $tailParagraph;
            }

            return $returnable;

        } catch (Exception $e) {
        }
    }

    public function parseParagraphBlock(string $el): ?array
    {
        $el = str_replace(["\r\n", "\r", "&npsp;", "\n"], "", $el);

        if($el)
        {
            return [
                'data' => [
                    'content' => $el,
                ],
                'type' => 'paragraph',
            ];
        }
    }

    public function element_to_obj($element)
    {
        if (isset($element->tagName)) {
            $obj = array('tag' => $element->tagName);
        }
        if (isset($element->attributes)) {
            foreach ($element->attributes as $attribute) {
                $obj[$attribute->name] = $attribute->value;
            }
        }
        if (isset($element->childNodes)) {
            foreach ($element->childNodes as $subElement) {
                if ($subElement->nodeType == XML_TEXT_NODE) {
                    $obj['html'] = $subElement->wholeText;
                } elseif ($subElement->nodeType == XML_CDATA_SECTION_NODE) {
                    $obj['html'] = $subElement->data;
                } else {
                    $obj['children'][] = $this->element_to_obj($subElement);
                }
            }
        }
        return (isset($obj)) ? $obj : null;
    }
}
