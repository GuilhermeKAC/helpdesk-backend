<?php

namespace Tests\Unit\Enums;

use App\Enums\TicketStatus;
use PHPUnit\Framework\TestCase;

class TicketStatusTest extends TestCase
{
    public function test_all_cases_have_correct_values(): void
    {
        $this->assertSame('open', TicketStatus::OPEN->value);
        $this->assertSame('in_progress', TicketStatus::IN_PROGRESS->value);
        $this->assertSame('pending', TicketStatus::PENDING->value);
        $this->assertSame('resolved', TicketStatus::RESOLVED->value);
        $this->assertSame('closed', TicketStatus::CLOSED->value);
    }

    public function test_label_returns_portuguese_name(): void
    {
        $this->assertSame('Aberto', TicketStatus::OPEN->label());
        $this->assertSame('Em Andamento', TicketStatus::IN_PROGRESS->label());
        $this->assertSame('Pendente', TicketStatus::PENDING->label());
        $this->assertSame('Resolvido', TicketStatus::RESOLVED->label());
        $this->assertSame('Fechado', TicketStatus::CLOSED->label());
    }

    public function test_color_returns_expected_color(): void
    {
        $this->assertSame('blue', TicketStatus::OPEN->color());
        $this->assertSame('yellow', TicketStatus::IN_PROGRESS->color());
        $this->assertSame('orange', TicketStatus::PENDING->color());
        $this->assertSame('green', TicketStatus::RESOLVED->color());
        $this->assertSame('gray', TicketStatus::CLOSED->color());
    }

    public function test_can_be_created_from_string(): void
    {
        $this->assertSame(TicketStatus::OPEN, TicketStatus::from('open'));
        $this->assertSame(TicketStatus::IN_PROGRESS, TicketStatus::from('in_progress'));
        $this->assertSame(TicketStatus::CLOSED, TicketStatus::from('closed'));
    }

    public function test_try_from_returns_null_for_invalid_value(): void
    {
        $this->assertNull(TicketStatus::tryFrom('invalid'));
        $this->assertNull(TicketStatus::tryFrom(''));
    }

    public function test_has_five_cases(): void
    {
        $this->assertCount(5, TicketStatus::cases());
    }
}
