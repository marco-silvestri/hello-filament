<?php

namespace App\Services;

use Exception;
use App\Models\Post;
use App\Models\Media;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Blade;
use App\Enums\Cms\PostDataBlockTypeEnum;
use Awcodes\Curator\View\Components\Glider;

class BlockLoader
{
    public static function renderDataBlock(array $dataBlock): string
    {
        try {
            $type = ucfirst($dataBlock['type']);
            $methodName = "compose{$type}";
            return self::$methodName($dataBlock['data']);
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }

    private static function composeParagraph($data): string
    {
        $content = strip_tags(
            $data['content'],
            ['<a>', '<ul>', '<li>', '<h1>', '<h2>', '<h3>', '<h4>', '<h5>', '<h6>', '<p>', '<img>', '<span>', '<em>', '<u>', '<ol>', '<small>', '<div>', '<br>', '<hr>']
        );
        return "<div class='font-brand
            text-display-500
            my-4 tracking-[0.8px] leading-[22px]'>{$content}</div>";
    }

    private static function composeImage($data): string
    {
        $blade = "<x-custom.glider
            class='object-cover mx-auto my-1 rounded-md'
            :media='{$data['image']}'
            fit='crop-center'
            format='webp'
            fallback='article_fallback'
            alt='{$data['alt']}'
            caption='{$data['alt']}'
            figclass='flex flex-col text-center my-8'/>";
        return Blade::render($blade);
    }

    private static function composeSlider($data): string
    {
        $images = [];
        foreach ($data['image'] as $image) {
            $media = Media::findOrFail($image);
            $alt = $media->alt ?? config('app.name');
            $images[] = [
                'media' => $image,
                'alt' => $alt,
                'caption' => $media->caption
            ];
        }
        $blade = "<x-custom.slider :images='\$images' :width='\$width' :height='\$height' />";

        return Blade::render($blade, ['images' => $images, 'width' => $data['width'], 'height' => $data['height']]);
    }

    private static function composeVideo($data): string
    {
        $videoId = explode('/', $data['url']);
        $videoId = end($videoId);
        return "<iframe class='mx-auto' src='https://www.youtube.com/embed/{$videoId}'> </iframe>";
    }

    private static function composeRelated_posts($data): string
    {
        $post = Post::find($data['related_posts']);
        $blade = "<a class='flex flex-row items-center my-2' href='{$post->slug->name}'>
        <x-curator-glider class='object-cover rounded' :media='{$post->featuredImage?->id}' fit='crop-center'
        format='webp' width='64' fallback='article_fallback' />
        <span class='ml-4 font-brand text-[12px]'>{$post->title}</span>
        </a>";
        return Blade::render($blade);
    }

    private static function composeIframe($data): string
    {
        return "<iframe class='w-full my-4 aspect-video'  src='{$data['src']}'> </iframe>";
    }

    private static function composeReview($data): string
    {
        if (count($data['parameters']) === 0) {
            //TODO implement block
            return "";
        } else {
            $summary = "<div><span class='font-bold'>Totale:</span> {$data['total_score']}</div>";
            $params = "";
            foreach ($data['parameters'] as $param) {
                $params .= "
                <div>
                <span class='font-bold'>{$param['key']}</span>
                {$param['value']}
                </div>
                ";
            }
            return "<div class='my-8'> <span class='text-xl font-bold'>I nostri voti</span>
            <div class='font-brand
            text-display-500
            my-4 tracking-[0.8px] leading-[22px]'>{$data['summary']}</div>
            {$params}
            {$summary}
            </div>";
        }
    }
}
