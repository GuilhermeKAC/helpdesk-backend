<?php

namespace Tests\Feature\Services;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Enums\UserRole;
use App\Models\Category;
use App\Models\Ticket;
use App\Models\User;
use App\Services\TicketService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketServiceTest extends TestCase
{
    use RefreshDatabase;

    private TicketService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new TicketService;
    }

    // ---- createTicket ----

    public function test_create_ticket_persists_to_database(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['sla_hours' => 24]);

        $ticket = $this->service->createTicket([
            'category_id' => $category->id,
            'title' => 'Meu ticket',
            'description' => 'Descrição do problema',
            'priority' => TicketPriority::HIGH->value,
        ], $user);

        $this->assertDatabaseHas('tickets', [
            'user_id' => $user->id,
            'category_id' => $category->id,
            'title' => 'Meu ticket',
            'status' => TicketStatus::OPEN->value,
            'priority' => TicketPriority::HIGH->value,
        ]);
    }

    public function test_create_ticket_generates_ticket_number(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        $ticket = $this->service->createTicket([
            'category_id' => $category->id,
            'title' => 'Test',
            'description' => 'Test',
            'priority' => TicketPriority::MEDIUM->value,
        ], $user);

        $this->assertMatchesRegularExpression('/^HD-\d{4}-\d{6}$/', $ticket->ticket_number);
    }

    public function test_create_ticket_sets_due_date_from_priority_sla(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        $ticket = $this->service->createTicket([
            'category_id' => $category->id,
            'title' => 'Urgente',
            'description' => 'Problema urgente',
            'priority' => TicketPriority::URGENT->value,
        ], $user);

        $this->assertNotNull($ticket->due_date);
        $diffHours = now()->diffInHours($ticket->due_date);
        $this->assertEqualsWithDelta(TicketPriority::URGENT->slaHours(), $diffHours, 1);
    }

    public function test_create_ticket_logs_created_activity(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        $ticket = $this->service->createTicket([
            'category_id' => $category->id,
            'title' => 'Test',
            'description' => 'Test',
            'priority' => TicketPriority::MEDIUM->value,
        ], $user);

        $this->assertDatabaseHas('ticket_activities', [
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'action' => 'created',
        ]);
    }

    public function test_create_ticket_auto_assigns_technician_from_category(): void
    {
        $technician = User::factory()->technician()->create();
        $category = Category::factory()->create([
            'auto_assign_technician_id' => $technician->id,
        ]);
        $user = User::factory()->create();

        $ticket = $this->service->createTicket([
            'category_id' => $category->id,
            'title' => 'Test',
            'description' => 'Test',
            'priority' => TicketPriority::MEDIUM->value,
        ], $user);

        $this->assertEquals($technician->id, $ticket->fresh()->technician_id);
    }

    // ---- assignTicket ----

    public function test_assign_ticket_sets_technician(): void
    {
        $technician = User::factory()->technician()->create();
        $ticket = Ticket::factory()->create(['status' => TicketStatus::OPEN->value]);

        $this->service->assignTicket($ticket, $technician->id, $technician);

        $this->assertEquals($technician->id, $ticket->fresh()->technician_id);
    }

    public function test_assign_ticket_changes_status_to_in_progress_when_open(): void
    {
        $technician = User::factory()->technician()->create();
        $ticket = Ticket::factory()->create(['status' => TicketStatus::OPEN->value]);

        $this->service->assignTicket($ticket, $technician->id, $technician);

        $this->assertSame(TicketStatus::IN_PROGRESS, $ticket->fresh()->status);
    }

    public function test_assign_ticket_logs_assigned_activity(): void
    {
        $technician = User::factory()->technician()->create();
        $ticket = Ticket::factory()->create();

        $this->service->assignTicket($ticket, $technician->id, $technician);

        $this->assertDatabaseHas('ticket_activities', [
            'ticket_id' => $ticket->id,
            'action' => 'assigned',
        ]);
    }

    // ---- changeStatus ----

    public function test_change_status_updates_ticket(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $ticket = Ticket::factory()->create(['status' => TicketStatus::OPEN->value]);

        $this->service->changeStatus($ticket, TicketStatus::IN_PROGRESS, $admin);

        $this->assertSame(TicketStatus::IN_PROGRESS, $ticket->fresh()->status);
    }

    public function test_change_status_to_resolved_sets_resolved_at_and_resolution_time(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $ticket = Ticket::factory()->create(['status' => TicketStatus::IN_PROGRESS->value]);

        $this->service->changeStatus($ticket, TicketStatus::RESOLVED, $admin);

        $fresh = $ticket->fresh();
        $this->assertNotNull($fresh->resolved_at);
        $this->assertNotNull($fresh->resolution_time);
        $this->assertIsInt($fresh->resolution_time);
    }

    public function test_change_status_to_closed_sets_closed_at(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $ticket = Ticket::factory()->create(['status' => TicketStatus::RESOLVED->value]);

        $this->service->changeStatus($ticket, TicketStatus::CLOSED, $admin);

        $this->assertNotNull($ticket->fresh()->closed_at);
    }

    public function test_change_status_logs_activity(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $ticket = Ticket::factory()->create(['status' => TicketStatus::OPEN->value]);

        $this->service->changeStatus($ticket, TicketStatus::IN_PROGRESS, $admin);

        $this->assertDatabaseHas('ticket_activities', [
            'ticket_id' => $ticket->id,
            'action' => 'status_changed',
        ]);
    }

    // ---- addReply ----

    public function test_add_reply_persists_message(): void
    {
        $user = User::factory()->create();
        $ticket = Ticket::factory()->create();

        $reply = $this->service->addReply($ticket, [
            'message' => 'Aqui está a resposta.',
            'is_internal' => false,
        ], $user);

        $this->assertDatabaseHas('ticket_replies', [
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'message' => 'Aqui está a resposta.',
            'is_internal' => false,
        ]);
    }

    public function test_add_reply_sets_response_time_on_first_technician_reply(): void
    {
        $technician = User::factory()->technician()->create();
        $ticket = Ticket::factory()->create(['response_time' => null]);

        $this->service->addReply($ticket, ['message' => 'Resposta técnica'], $technician);

        $this->assertNotNull($ticket->fresh()->response_time);
    }

    public function test_add_reply_logs_replied_activity(): void
    {
        $user = User::factory()->create();
        $ticket = Ticket::factory()->create();

        $this->service->addReply($ticket, ['message' => 'Resposta'], $user);

        $this->assertDatabaseHas('ticket_activities', [
            'ticket_id' => $ticket->id,
            'action' => 'replied',
        ]);
    }

    // ---- getFilteredTickets ----

    public function test_customer_sees_only_own_tickets(): void
    {
        $customer = User::factory()->create(['role' => UserRole::CUSTOMER]);
        $other = User::factory()->create(['role' => UserRole::CUSTOMER]);

        Ticket::factory()->count(3)->create(['user_id' => $customer->id]);
        Ticket::factory()->count(2)->create(['user_id' => $other->id]);

        $result = $this->service->getFilteredTickets([], $customer);

        $this->assertCount(3, $result->items());
    }

    public function test_admin_sees_all_tickets(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        Ticket::factory()->count(5)->create();

        $result = $this->service->getFilteredTickets([], $admin);

        $this->assertCount(5, $result->items());
    }

    public function test_filter_by_status(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        Ticket::factory()->count(2)->create(['status' => TicketStatus::OPEN->value]);
        Ticket::factory()->count(3)->create(['status' => TicketStatus::CLOSED->value]);

        $result = $this->service->getFilteredTickets(['status' => 'open'], $admin);

        $this->assertCount(2, $result->items());
    }
}
