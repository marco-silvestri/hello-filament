<?php

namespace App\Enums\Cms;

use App\Traits\HasArray;
use Filament\Support\Contracts\HasLabel;

enum NewsletterStatusEnum :string implements HasLabel{
    use HasArray;

    case DRAFT = 'draft';
    case SENT = 'sent';
    case LOADING = 'loading';
    case LOADED = 'loaded';
    case LOADING_ERROR = 'loading_error';

    public function getLabel(): string
    {
        return match($this) {
            self::DRAFT => 'Head',
            self::SENT => 'Body',
            self::LOADING => 'Footer',
            self::LOADED => 'Body',
            self::LOADING_ERROR => 'Footer',
        };
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
