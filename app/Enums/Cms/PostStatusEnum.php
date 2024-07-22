<?php

namespace App\Enums\Cms;

use App\Traits\HasArray;
use Filament\Support\Contracts\HasLabel;

enum PostStatusEnum: string implements HasLabel
{
    use HasArray;

    case DRAFT = 'draft';
    case PUBLISH = 'publish';
    case PLANNED = 'planned';

    public function getLabel(): string
    {
        return match ($this) {
            self::DRAFT => __('posts.lbl-draft'),
            self::PUBLISH => __('posts.lbl-publish'),
            self::PLANNED => __('posts.lbl-planned')
        };
    }

    public function getValue(): string
    {
        return match ($this) {
            self::DRAFT => self::DRAFT->value,
            self::PUBLISH => self::PUBLISH->value,
            self::PLANNED =>  self::PLANNED->value
        };
    }

    public static function mapLegacy(string $legacy)
    {
        return match ($legacy) {
            'draft' => self::DRAFT,
            'publish' => self::PUBLISH,
            'future' => self::PUBLISH,
        };
    }
}
