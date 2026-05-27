<?php

namespace App\Services;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Enums\UserRole;
use App\Models\Category;
use App\Models\Ticket;
use App\Models\TicketActivity;
use App\Models\TicketReply;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class TicketService
{
    public function createTicket(array $data, User $user): Ticket
    {
        return DB::transaction(function () use ($data, $user) {
            $category = Category::findOrFail($data['category_id']);

            $ticket = Ticket::create([
                'user_id' => $user->id,
                'category_id' => $data['category_id'],
                'title' => $data['title'],
                'description' => $data['description'],
                'status' => TicketStatus::OPEN->value,
                'priority' => $data['priority'],
                'due_date' => now()->addHours(
                    TicketPriority::from($data['priority'])->slaHours()
                ),
            ]);

            $ticket->refresh();

            if ($category->auto_assign_technician_id) {
                $this->assignTicket($ticket, $category->auto_assign_technician_id, null);
            }

            $this->logActivity($ticket, null, $user, 'created', null, [
                'status' => $ticket->status->value,
                'priority' => $ticket->priority->value,
            ]);

            return $ticket->load(['user', 'category', 'technician']);
        });
    }

    public function assignTicket(Ticket $ticket, int $technicianId, ?User $actor): Ticket
    {
        return DB::transaction(function () use ($ticket, $technicianId, $actor) {
            $oldTechnicianId = $ticket->technician_id;

            $ticket->update([
                'technician_id' => $technicianId,
                'assigned_at' => $ticket->assigned_at ?? now(),
                'status' => $ticket->status === TicketStatus::OPEN
                    ? TicketStatus::IN_PROGRESS->value
                    : $ticket->status->value,
            ]);

            $this->logActivity($ticket, null, $actor, 'assigned', [
                'technician_id' => $oldTechnicianId,
            ], [
                'technician_id' => $technicianId,
            ]);

            return $ticket->fresh(['user', 'category', 'technician']);
        });
    }

    public function changeStatus(Ticket $ticket, TicketStatus $newStatus, User $actor): Ticket
    {
        return DB::transaction(function () use ($ticket, $newStatus, $actor) {
            $oldStatus = $ticket->status;
            $updates = ['status' => $newStatus->value];

            if ($newStatus === TicketStatus::RESOLVED && ! $ticket->resolved_at) {
                $updates['resolved_at'] = now();
                $updates['resolution_time'] = (int) $ticket->created_at->diffInMinutes(now());
            }

            if ($newStatus === TicketStatus::CLOSED && ! $ticket->closed_at) {
                $updates['closed_at'] = now();
            }

            $ticket->update($updates);

            $this->logActivity($ticket, null, $actor, 'status_changed', [
                'status' => $oldStatus->value,
            ], [
                'status' => $newStatus->value,
            ]);

            return $ticket->fresh(['user', 'category', 'technician']);
        });
    }

    public function addReply(Ticket $ticket, array $data, User $user): TicketReply
    {
        return DB::transaction(function () use ($ticket, $data, $user) {
            $reply = $ticket->replies()->create([
                'user_id' => $user->id,
                'message' => $data['message'],
                'is_internal' => $data['is_internal'] ?? false,
            ]);

            if (! $ticket->response_time && $user->role !== UserRole::CUSTOMER) {
                $ticket->update([
                    'response_time' => (int) $ticket->created_at->diffInMinutes(now()),
                ]);
            }

            $this->logActivity($ticket, null, $user, 'replied', null, [
                'reply_id' => $reply->id,
                'is_internal' => $reply->is_internal,
            ]);

            return $reply->load('user');
        });
    }

    public function getFilteredTickets(array $filters, User $user): LengthAwarePaginator
    {
        $query = Ticket::with(['user', 'category', 'technician'])
            ->withCount('replies');

        if ($user->role === UserRole::CUSTOMER) {
            $query->forUser($user->id);
        } elseif ($user->role === UserRole::TECHNICIAN) {
            $query->where(function ($q) use ($user) {
                $q->forTechnician($user->id)->orWhereNull('technician_id');
            });
        }

        if (! empty($filters['status'])) {
            $query->byStatus(TicketStatus::from($filters['status']));
        }

        if (! empty($filters['priority'])) {
            $query->byPriority(TicketPriority::from($filters['priority']));
        }

        if (! empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (! empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('title', 'ilike', "%{$filters['search']}%")
                    ->orWhere('ticket_number', 'ilike', "%{$filters['search']}%");
            });
        }

        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortDir = $filters['sort_dir'] ?? 'desc';
        $allowed = ['created_at', 'updated_at', 'priority', 'status', 'due_date'];

        $query->orderBy(in_array($sortBy, $allowed) ? $sortBy : 'created_at', $sortDir === 'asc' ? 'asc' : 'desc');

        return $query->paginate($filters['per_page'] ?? 15);
    }

    private function logActivity(Ticket $ticket, ?string $ipAddress, ?User $user, string $action, ?array $oldValue, ?array $newValue): void
    {
        TicketActivity::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user?->id,
            'action' => $action,
            'old_value' => $oldValue,
            'new_value' => $newValue,
            'ip_address' => $ipAddress,
        ]);
    }
}
