<?php

namespace App\Enums;

enum HookEnum {

    case HEAD;
    case BODY;
    case FOOTER;

    public function label(): string
    {
        return match($this) {
            self::HEAD => 'Head',
            self::BODY => 'Body',
            self::FOOTER => 'Footer',
        };
    }

    public function value(): string
    {
        return match($this) {
            self::HEAD => 'head',
            self::BODY => 'body',
            self::FOOTER => 'footer',
        };
    }

    public static function getByValue($value):HookEnum
    {
        return match($value) {
            self::HEAD->value() => self::HEAD,
            self::BODY->value() => self::BODY,
            self::FOOTER->value() => self::FOOTER,
        };
    }
}
