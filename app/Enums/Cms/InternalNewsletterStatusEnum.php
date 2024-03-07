<?php

namespace App\Enums\Cms;

use App\Traits\HasArray;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum InternalNewsletterStatusEnum :string implements HasLabel, HasColor, HasIcon{
    use HasArray;

    case DRAFT = 'draft';
    case SENT = 'sent';
    case LOADING = 'loading';
    case LOADED = 'loaded';
    case LOADING_ERROR = 'loading_error';

    public function getLabel(): string
    {
        return match($this) {
            self::DRAFT => 'draft',
            self::SENT => 'sent',
            self::LOADING => 'loading',
            self::LOADED => 'loaded',
            self::LOADING_ERROR => 'loading_error',
        };
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::DRAFT => 'gray',
            self::SENT => 'info',
            self::LOADING => 'warning',
            self::LOADED => 'success',
            self::LOADING_ERROR => 'danger',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::DRAFT => 'heroicon-c-pencil-square',
            self::SENT => 'heroicon-c-check',
            self::LOADING => 'heroicon-c-arrow-path',
            self::LOADED => 'heroicon-c-archive-box-arrow-down',
            self::LOADING_ERROR => 'heroicon-c-archive-box-x-mark',
        };
    }

    public static function mapStatusFromApi(string $status):?string{

        return match($status){
            '0' => self::DRAFT->getValue(),
            '1' => self::SENT->getValue(),
            '2' => self::LOADING->getValue(),
            '3' => self::LOADED->getValue(),
            '4' => self::LOADING_ERROR->getValue(),
            default => null,
        };
    }
}
