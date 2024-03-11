<?php

namespace App\Enums\Cms;

use App\Traits\HasArray;
use Filament\Support\Contracts\HasLabel;

enum MenuOptionsEnum: string implements HasLabel
{
    use HasArray;

    case PAGE = 'page';
    case CATEGORY = 'category';
    case TAG = 'tag';
    case EXTERNAL_URL = 'external_url';

    public function getLabel(): string
    {
        return match ($this) {
            self::PAGE => __('common.ent-page'),
            self::CATEGORY => __('common.ent-category'),
            self::TAG => __('common.ent-tag'),
            self::EXTERNAL_URL => __('common.lbl-external-url'),
        };
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
