<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case TECHNICIAN = 'technician';
    case CUSTOMER = 'customer';

    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'Administrador',
            self::TECHNICIAN => 'Técnico',
            self::CUSTOMER => 'Cliente',
        };
    }
}
