<?php

namespace Tests\Feature\Tickets;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Enums\UserRole;
use App\Models\Category;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketTest extends TestCase
{
    use RefreshDatabase;

    // ---- index ----

    public function test_index_requires_authentication(): void
    {
        $this->getJson('/api/v1/tickets')->assertStatus(401);
    }

    public function test_customer_sees_only_own_tickets(): void
    {
        $customer = User::factory()->create(['role' => UserRole::CUSTOMER]);
        $other = User::factory()->create(['role' => UserRole::CUSTOMER]);

        Ticket::factory()->count(2)->create(['user_id' => $customer->id]);
        Ticket::factory()->count(3)->create(['user_id' => $other->id]);

        $this->actingAs($customer)
            ->getJson('/api/v1/tickets')
            ->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    public function test_admin_sees_all_tickets(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        Ticket::factory()->count(5)->create();

        $this->actingAs($admin)
            ->getJson('/api/v1/tickets')
            ->assertStatus(200)
            ->assertJsonCount(5, 'data');
    }

    public function test_index_filters_by_status(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        Ticket::factory()->count(2)->create(['status' => TicketStatus::OPEN->value]);
        Ticket::factory()->count(3)->create(['status' => TicketStatus::CLOSED->value]);

        $this->actingAs($admin)
            ->getJson('/api/v1/tickets?status=open')
            ->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    // ---- store ----

    public function test_store_requires_authentication(): void
    {
        $this->postJson('/api/v1/tickets')->assertStatus(401);
    }

    public function test_customer_can_create_ticket(): void
    {
        $customer = User::factory()->create(['role' => UserRole::CUSTOMER]);
        $category = Category::factory()->create();

        $response = $this->actingAs($customer)->postJson('/api/v1/tickets', [
            'category_id' => $category->id,
            'title' => 'Problema com acesso',
            'description' => 'Não consigo acessar o sistema.',
            'priority' => TicketPriority::HIGH->value,
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['data' => ['id', 'ticket_number', 'status', 'priority']])
            ->assertJsonPath('data.status', TicketStatus::OPEN->value);
    }

    public function test_store_fails_without_required_fields(): void
    {
        $customer = User::factory()->create(['role' => UserRole::CUSTOMER]);

        $this->actingAs($customer)
            ->postJson('/api/v1/tickets', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['category_id', 'title', 'description', 'priority']);
    }

    public function test_store_fails_with_invalid_category(): void
    {
        $customer = User::factory()->create(['role' => UserRole::CUSTOMER]);

        $this->actingAs($customer)
            ->postJson('/api/v1/tickets', [
                'category_id' => 9999,
                'title' => 'Test',
                'description' => 'Test',
                'priority' => TicketPriority::LOW->value,
            ])->assertStatus(422)
            ->assertJsonValidationErrors(['category_id']);
    }

    // ---- show ----

    public function test_show_requires_authentication(): void
    {
        $ticket = Ticket::factory()->create();
        $this->getJson("/api/v1/tickets/{$ticket->id}")->assertStatus(401);
    }

    public function test_customer_can_view_own_ticket(): void
    {
        $customer = User::factory()->create(['role' => UserRole::CUSTOMER]);
        $ticket = Ticket::factory()->create(['user_id' => $customer->id]);

        $this->actingAs($customer)
            ->getJson("/api/v1/tickets/{$ticket->id}")
            ->assertStatus(200)
            ->assertJsonPath('data.id', $ticket->id);
    }

    public function test_customer_cannot_view_other_ticket(): void
    {
        $customer = User::factory()->create(['role' => UserRole::CUSTOMER]);
        $other = User::factory()->create(['role' => UserRole::CUSTOMER]);
        $ticket = Ticket::factory()->create(['user_id' => $other->id]);

        $this->actingAs($customer)
            ->getJson("/api/v1/tickets/{$ticket->id}")
            ->assertStatus(403);
    }

    // ---- assign ----

    public function test_assign_requires_authentication(): void
    {
        $ticket = Ticket::factory()->create();
        $this->postJson("/api/v1/tickets/{$ticket->id}/assign")->assertStatus(401);
    }

    public function test_technician_can_assign_ticket(): void
    {
        $technician = User::factory()->technician()->create();
        $ticket = Ticket::factory()->create(['status' => TicketStatus::OPEN->value]);

        $this->actingAs($technician)
            ->postJson("/api/v1/tickets/{$ticket->id}/assign", [
                'technician_id' => $technician->id,
            ])->assertStatus(200)
            ->assertJsonPath('data.status', TicketStatus::IN_PROGRESS->value);
    }

    public function test_customer_cannot_assign_ticket(): void
    {
        $customer = User::factory()->create(['role' => UserRole::CUSTOMER]);
        $technician = User::factory()->technician()->create();
        $ticket = Ticket::factory()->create(['user_id' => $customer->id]);

        $this->actingAs($customer)
            ->postJson("/api/v1/tickets/{$ticket->id}/assign", [
                'technician_id' => $technician->id,
            ])->assertStatus(403);
    }

    // ---- status ----

    public function test_technician_can_change_status(): void
    {
        $technician = User::factory()->technician()->create();
        $ticket = Ticket::factory()->create(['status' => TicketStatus::OPEN->value]);

        $this->actingAs($technician)
            ->postJson("/api/v1/tickets/{$ticket->id}/status", [
                'status' => TicketStatus::IN_PROGRESS->value,
            ])->assertStatus(200)
            ->assertJsonPath('data.status', TicketStatus::IN_PROGRESS->value);
    }

    public function test_customer_cannot_change_status(): void
    {
        $customer = User::factory()->create(['role' => UserRole::CUSTOMER]);
        $ticket = Ticket::factory()->create(['user_id' => $customer->id]);

        $this->actingAs($customer)
            ->postJson("/api/v1/tickets/{$ticket->id}/status", [
                'status' => TicketStatus::CLOSED->value,
            ])->assertStatus(403);
    }

    // ---- reply ----

    public function test_customer_can_reply_own_ticket(): void
    {
        $customer = User::factory()->create(['role' => UserRole::CUSTOMER]);
        $ticket = Ticket::factory()->create(['user_id' => $customer->id]);

        $this->actingAs($customer)
            ->postJson("/api/v1/tickets/{$ticket->id}/reply", [
                'message' => 'Informação adicional.',
            ])->assertStatus(201)
            ->assertJsonPath('data.message', 'Informação adicional.');
    }

    public function test_customer_cannot_reply_with_internal_flag(): void
    {
        $customer = User::factory()->create(['role' => UserRole::CUSTOMER]);
        $ticket = Ticket::factory()->create(['user_id' => $customer->id]);

        $response = $this->actingAs($customer)
            ->postJson("/api/v1/tickets/{$ticket->id}/reply", [
                'message' => 'Nota interna.',
                'is_internal' => true,
            ])->assertStatus(201);

        $this->assertFalse($response->json('data.is_internal'));
    }
}
