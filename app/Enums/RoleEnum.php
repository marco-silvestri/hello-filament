<?php

namespace App\Enums;

use App\Traits\HasArray;

enum RoleEnum: string
{
    use HasArray;

    case SUPERADMIN = 'superadmin';
    case ADMIN = 'admin';
    case EDITOR = 'editor';
    case AUTHOR = 'author';
    case CUSTOMER = 'customer';


    public function getLabel(): string
    {
        return match ($this) {
            self::SUPERADMIN => 'Super admin',
            self::ADMIN => 'Admin',
            self::EDITOR => 'Editor',
            self::AUTHOR => 'Author',
            self::CUSTOMER => 'customer',
        };
    }

    public function getValue(): string
    {
        return match ($this) {
            self::SUPERADMIN => self::SUPERADMIN->value,
            self::ADMIN => self::ADMIN->value,
            self::EDITOR => self::EDITOR->value,
            self::AUTHOR => self::AUTHOR->value,
            self::CUSTOMER => self::CUSTOMER->value,
        };
    }
}
