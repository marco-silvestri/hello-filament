<?php

namespace App\Enums\Cms;

use App\Traits\HasArray;
use Filament\Support\Contracts\HasLabel;

enum DisplayableAsEnum :string implements HasLabel{

    use HasArray;

    case STRIP = 'strip';
    case HIGHLIGHT = 'highlight';
    case HEADER = 'header';
    case SHOWCASE = 'showcase';
    case HIGHLIGHTED_STRIP = 'highlighted-strip';

    public function getLabel(): string
    {
        return match($this) {
            self::STRIP => 'Strip',
            self::HIGHLIGHT => 'Highlight',
            self::HEADER => 'Header',
            self::SHOWCASE => 'Showcase',
            self::HIGHLIGHTED_STRIP => 'Highlighted Strip',
        };
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
