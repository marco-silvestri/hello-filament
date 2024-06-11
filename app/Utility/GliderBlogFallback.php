<?php

namespace App\Utility;

use Awcodes\Curator\Glide\GliderFallback;

class GliderBlogFallback extends GliderFallback
{
    public function getAlt(): string
    {
        return config('app.name');
    }

    public function getHeight(): int
    {
        return 300;
    }

    public function getKey(): string
    {
        return 'article_fallback';
    }

    public function getSource(): string
    {
        return asset('default-fallback-image.webp');
    }

    public function getType(): string
    {
        return 'image/webp';
    }

    public function getWidth(): int
    {
        return 420;
    }
}
