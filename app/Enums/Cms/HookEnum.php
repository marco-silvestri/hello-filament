<?php

namespace App\Enums\Cms;

use App\Traits\HasArray;
use Filament\Support\Contracts\HasLabel;

enum HookEnum :string implements HasLabel{

    use HasArray;

    case HEAD = 'head';
    case BODY = 'body';
    case FOOTER = 'footer';

    public function getLabel(): string
    {
        return match($this) {
            self::HEAD => 'Head',
            self::BODY => 'Body',
            self::FOOTER => 'Footer',
        };
    }

    public function getValue(): string
    {
        return match($this) {
            self::HEAD => self::HEAD->value,
            self::BODY => self::BODY->value,
            self::FOOTER => self::FOOTER->value,
        };
    }
}
