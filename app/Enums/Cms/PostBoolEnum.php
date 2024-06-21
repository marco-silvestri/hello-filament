<?php

namespace App\Enums\Cms;

use App\Traits\HasArray;
use Filament\Support\Contracts\HasLabel;

enum PostBoolEnum: string implements HasLabel
{
    use HasArray;

    case YES = '1';
    case NO = '0';

    public function getLabel(): string
    {
        return match ($this) {
            self::YES => __('common.lbl-yes'),
            self::NO => __('common.lbl-no')
        };
    }

    public function getValue(): string
    {
        return match ($this) {
            self::YES => self::YES->value,
            self::NO => self::NO->value
        };
    }

    public static function mapLegacy(string $legacy)
    {
        return match ($legacy) {
           '0' => self::NO,
            '1' => self::YES,
        };
    }
}
