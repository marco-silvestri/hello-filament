<?php

namespace App\Utility;

use Awcodes\Curator\Glide\GliderFallback;

class GliderCustomFallback extends GliderFallback
{
    public function getAlt(): string
    {
        return 'boring fallback image';
    }

    public function getHeight(): int
    {
        return 300;
    }

    public function getKey(): string
    {
        return 'card_fallback';
    }

    public function getSource(): string
    {
        return asset('placeholder-img.jpg');
    }

    public function getType(): string
    {
        return 'image/jpg';
    }

    public function getWidth(): int
    {
        return 420;
    }
}
