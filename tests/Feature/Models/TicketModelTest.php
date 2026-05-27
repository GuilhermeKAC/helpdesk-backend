<?php

namespace Tests\Feature\Models;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Models\Category;
use App\Models\Ticket;
use App\Models\TicketActivity;
use App\Models\TicketReply;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketModelTest extends TestCase
{
    use RefreshDatabase;

    private function createTicket(array $overrides = []): Ticket
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        return Ticket::factory()->create(array_merge([
            'user_id' => $user->id,
            'category_id' => $category->id,
        ], $overrides));
    }

    public function test_status_is_cast_to_enum(): void
    {
        $ticket = $this->createTicket(['status' => TicketStatus::OPEN->value]);

        $this->assertInstanceOf(TicketStatus::class, $ticket->fresh()->status);
        $this->assertSame(TicketStatus::OPEN, $ticket->fresh()->status);
    }

    public function test_priority_is_cast_to_enum(): void
    {
        $ticket = $this->createTicket(['priority' => TicketPriority::URGENT->value]);

        $this->assertInstanceOf(TicketPriority::class, $ticket->fresh()->priority);
        $this->assertSame(TicketPriority::URGENT, $ticket->fresh()->priority);
    }

    public function test_metadata_is_cast_to_array(): void
    {
        $ticket = $this->createTicket(['metadata' => ['source' => 'email']]);

        $this->assertIsArray($ticket->fresh()->metadata);
        $this->assertSame('email', $ticket->fresh()->metadata['source']);
    }

    public function test_ticket_number_is_generated_by_trigger(): void
    {
        $ticket = $this->createTicket();

        $this->assertNotNull($ticket->fresh()->ticket_number);
        $this->assertMatchesRegularExpression('/^HD-\d{4}-\d{6}$/', $ticket->fresh()->ticket_number);
    }

    public function test_scope_open_filters_by_open_status(): void
    {
        $this->createTicket(['status' => TicketStatus::OPEN->value]);
        $this->createTicket(['status' => TicketStatus::CLOSED->value]);

        $this->assertCount(1, Ticket::open()->get());
    }

    public function test_scope_by_status(): void
    {
        $this->createTicket(['status' => TicketStatus::IN_PROGRESS->value]);
        $this->createTicket(['status' => TicketStatus::OPEN->value]);

        $this->assertCount(1, Ticket::byStatus(TicketStatus::IN_PROGRESS)->get());
    }

    public function test_scope_by_priority(): void
    {
        $this->createTicket(['priority' => TicketPriority::URGENT->value]);
        $this->createTicket(['priority' => TicketPriority::LOW->value]);

        $this->assertCount(1, Ticket::byPriority(TicketPriority::URGENT)->get());
    }

    public function test_scope_for_user(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $cat = Category::factory()->create();

        Ticket::factory()->create(['user_id' => $user->id, 'category_id' => $cat->id]);
        Ticket::factory()->create(['user_id' => $other->id, 'category_id' => $cat->id]);

        $this->assertCount(1, Ticket::forUser($user->id)->get());
    }

    public function test_scope_for_technician(): void
    {
        $tech = User::factory()->technician()->create();
        $cat = Category::factory()->create();
        $user = User::factory()->create();

        Ticket::factory()->create(['user_id' => $user->id, 'category_id' => $cat->id, 'technician_id' => $tech->id]);
        Ticket::factory()->create(['user_id' => $user->id, 'category_id' => $cat->id, 'technician_id' => null]);

        $this->assertCount(1, Ticket::forTechnician($tech->id)->get());
    }

    public function test_soft_delete_works(): void
    {
        $ticket = $this->createTicket();
        $ticket->delete();

        $this->assertSoftDeleted($ticket);
        $this->assertNull(Ticket::find($ticket->id));
    }

    public function test_has_replies_relationship(): void
    {
        $ticket = $this->createTicket();
        TicketReply::factory()->count(2)->create(['ticket_id' => $ticket->id, 'user_id' => $ticket->user_id]);

        $this->assertCount(2, $ticket->replies);
    }

    public function test_has_activities_relationship(): void
    {
        $ticket = $this->createTicket();
        TicketActivity::factory()->count(3)->create(['ticket_id' => $ticket->id]);

        $this->assertCount(3, $ticket->activities);
    }
}
