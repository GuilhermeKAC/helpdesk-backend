<?php

namespace Tests\Unit\Enums;

use App\Enums\TicketPriority;
use PHPUnit\Framework\TestCase;

class TicketPriorityTest extends TestCase
{
    public function test_all_cases_have_correct_values(): void
    {
        $this->assertSame('low', TicketPriority::LOW->value);
        $this->assertSame('medium', TicketPriority::MEDIUM->value);
        $this->assertSame('high', TicketPriority::HIGH->value);
        $this->assertSame('urgent', TicketPriority::URGENT->value);
    }

    public function test_label_returns_portuguese_name(): void
    {
        $this->assertSame('Baixa', TicketPriority::LOW->label());
        $this->assertSame('Média', TicketPriority::MEDIUM->label());
        $this->assertSame('Alta', TicketPriority::HIGH->label());
        $this->assertSame('Urgente', TicketPriority::URGENT->label());
    }

    public function test_sla_hours_returns_correct_values(): void
    {
        $this->assertSame(72, TicketPriority::LOW->slaHours());
        $this->assertSame(48, TicketPriority::MEDIUM->slaHours());
        $this->assertSame(24, TicketPriority::HIGH->slaHours());
        $this->assertSame(4, TicketPriority::URGENT->slaHours());
    }

    public function test_higher_priority_has_shorter_sla(): void
    {
        $this->assertGreaterThan(TicketPriority::HIGH->slaHours(), TicketPriority::MEDIUM->slaHours());
        $this->assertGreaterThan(TicketPriority::URGENT->slaHours(), TicketPriority::HIGH->slaHours());
    }

    public function test_color_returns_expected_color(): void
    {
        $this->assertSame('green', TicketPriority::LOW->color());
        $this->assertSame('blue', TicketPriority::MEDIUM->color());
        $this->assertSame('orange', TicketPriority::HIGH->color());
        $this->assertSame('red', TicketPriority::URGENT->color());
    }

    public function test_can_be_created_from_string(): void
    {
        $this->assertSame(TicketPriority::URGENT, TicketPriority::from('urgent'));
        $this->assertSame(TicketPriority::LOW, TicketPriority::from('low'));
    }

    public function test_try_from_returns_null_for_invalid_value(): void
    {
        $this->assertNull(TicketPriority::tryFrom('critical'));
        $this->assertNull(TicketPriority::tryFrom(''));
    }

    public function test_has_four_cases(): void
    {
        $this->assertCount(4, TicketPriority::cases());
    }
}
