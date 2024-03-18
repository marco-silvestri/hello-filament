<?php

namespace App\Enums\Cms;

use App\Traits\HasArray;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum PostAccessEnum :string implements HasLabel, HasColor, HasIcon {

    use HasArray;

    case FREE = 'free';
    case REGISTERED = 'registered';
    case PREMIUM = 'premium';

    public function getLabel(): string
    {
        return match($this) {
            self::FREE => __('posts.fl-access-free'),
            self::REGISTERED => __('posts.fl-access-registered'),
            self::PREMIUM => __('posts.fl-access-premium'),
        };
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::FREE => 'warning',
            self::REGISTERED => 'success',
            self::PREMIUM => 'danger',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::FREE => 'heroicon-m-hand-raised',
            self::REGISTERED => 'heroicon-m-hand-thumb-up',
            self::PREMIUM => 'heroicon-m-hand-thumb-down',
        };
    }

}
