<?php

namespace App\Enums;

enum RoleEnum: string
{

    case SUPERADMIN = 'superadmin';
    case ADMIN = 'admin';
    case EDITOR = 'editor';
    case AUTHOR = 'author';

    public function getLabel(): string
    {
        return match ($this) {
            self::SUPERADMIN => 'Super admin',
            self::ADMIN => 'Admin',
            self::EDITOR => 'Editor',
            self::AUTHOR => 'Author',
        };
    }
}
