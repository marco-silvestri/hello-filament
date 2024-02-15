<?php

namespace App\Enums\Cms;

use App\Traits\HasArray;
use Filament\Support\Contracts\HasLabel;

enum PostStatusEnum: string implements HasLabel
{
    use HasArray;

    case DRAFT = 'draft';
    case PUBLISH = 'publish';
    case FUTURE = 'future';

    public function getLabel(): string
    {
        return match ($this) {
            self::DRAFT => __('posts.lbl-draft'),
            self::PUBLISH => __('posts.lbl-publish'),
            self::FUTURE => __('posts.lbl-future'),
        };
    }

    public function getValue(): string
    {
        return match ($this) {
            self::DRAFT => self::DRAFT->value,
            self::PUBLISH => self::PUBLISH->value,
            self::FUTURE => self::FUTURE->value,
        };
    }
}