<?php

namespace App\Enums;

enum TicketPriority: string
{
    case LOW = 'low';
    case MEDIUM = 'medium';
    case HIGH = 'high';
    case URGENT = 'urgent';

    public function label(): string
    {
        return match ($this) {
            self::LOW => 'Baixa',
            self::MEDIUM => 'Média',
            self::HIGH => 'Alta',
            self::URGENT => 'Urgente',
        };
    }

    public function slaHours(): int
    {
        return match ($this) {
            self::LOW => 72,
            self::MEDIUM => 48,
            self::HIGH => 24,
            self::URGENT => 4,
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::LOW => 'green',
            self::MEDIUM => 'blue',
            self::HIGH => 'orange',
            self::URGENT => 'red',
        };
    }
}
