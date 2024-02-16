<?php

namespace App\Enums\Cms;

use App\Traits\HasArray;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum CommentStatusEnum :string implements HasLabel, HasColor, HasIcon {

    use HasArray;

    case AWAITING_MODERATION = 'awaiting-moderation';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';

    public function getLabel(): string
    {
        return match($this) {
            self::AWAITING_MODERATION => __('comments.lbl-awaiting'),
            self::APPROVED => __('comments.lbl-approved'),
            self::REJECTED => __('comments.lbl-rejected'),
        };
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::AWAITING_MODERATION => 'warning',
            self::APPROVED => 'success',
            self::REJECTED => 'danger',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::AWAITING_MODERATION => 'heroicon-m-hand-raised',
            self::APPROVED => 'heroicon-m-hand-thumb-up',
            self::REJECTED => 'heroicon-m-hand-thumb-down',
        };
    }

}
