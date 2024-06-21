<?php

namespace App\Enums\Cms;

use App\Traits\HasArray;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum CommunicationStatusEnum :string implements HasLabel, HasColor {

    use HasArray;

    case SCHEDULED = 'scheduled';
    case SENT = 'sent';
    case FROZEN = 'frozen';
    case CANCELLED = 'cancelled';

    public function getLabel(): string
    {
        return match($this) {
            self::SCHEDULED => __('comments.lbl-awaiting'),
            self::SENT => __('comments.lbl-approved'),
            self::FROZEN => __('comments.lbl-rejected'),
            self::CANCELLED => __('comments.lbl-rejected'),
        };
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::SCHEDULED => 'warning',
            self::SENT => 'success',
            self::FROZEN => 'info',
            self::CANCELLED => 'error',
        };
    }
}
