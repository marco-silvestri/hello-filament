<?php

namespace App\Enums\Cms;

use App\Traits\HasArray;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Support\Contracts\HasLabel;

enum WdgSponsorTypeEnum: string implements HasLabel
{
    use HasArray;

    case BADGE = 'badge';

    public function getLabel(): string
    {
        return match ($this) {
            self::BADGE => __('wdg_sponsor.lbl-badge'),
        };
    }

    public function getValue(): string
    {
        return match ($this) {
            self::BADGE => self::BADGE->value,
        };
    }

    public function getView()
    {
        return match ($this) {
            self::BADGE => 'filament.forms.components.widgets.badge',
        };
    }

    public static function getFilamentBlocks(string|null $type)
    {
        return match ($type) {
            self::BADGE->value => [
                FileUpload::make('json_content.immagine')
                    ->disk('public')
                    ->directory('sponsor'),
                TextInput::make('json_content.alt')
                    ->label('alt text'),
                ViewField::make('preview')
                    ->view(self::BADGE->getView())
                    ->hiddenOn('create')
            ],
            default => []
        };
    }
}
