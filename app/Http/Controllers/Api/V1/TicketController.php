<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\TicketStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\AddReplyRequest;
use App\Http\Requests\Api\V1\AssignTicketRequest;
use App\Http\Requests\Api\V1\ChangeStatusRequest;
use App\Http\Requests\Api\V1\StoreTicketRequest;
use App\Http\Resources\TicketActivityResource;
use App\Http\Resources\TicketReplyResource;
use App\Http\Resources\TicketResource;
use App\Models\Ticket;
use App\Services\TicketService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TicketController extends Controller
{
    public function __construct(private readonly TicketService $ticketService) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $tickets = $this->ticketService->getFilteredTickets(
            $request->only(['status', 'priority', 'category_id', 'search', 'sort_by', 'sort_dir', 'per_page']),
            $request->user()
        );

        return TicketResource::collection($tickets);
    }

    public function store(StoreTicketRequest $request): JsonResponse
    {
        $ticket = $this->ticketService->createTicket($request->validated(), $request->user());

        return response()->json(['data' => new TicketResource($ticket)], 201);
    }

    public function show(Request $request, Ticket $ticket): JsonResponse
    {
        $this->authorizeView($request->user(), $ticket);

        $ticket->load(['user', 'technician', 'category', 'attachments'])
            ->loadCount('replies');

        return response()->json(['data' => new TicketResource($ticket)]);
    }

    public function assign(AssignTicketRequest $request, Ticket $ticket): JsonResponse
    {
        $this->authorizeModify($request->user(), $ticket);

        $ticket = $this->ticketService->assignTicket(
            $ticket,
            $request->technician_id,
            $request->user()
        );

        return response()->json(['data' => new TicketResource($ticket)]);
    }

    public function changeStatus(ChangeStatusRequest $request, Ticket $ticket): JsonResponse
    {
        $this->authorizeModify($request->user(), $ticket);

        $ticket = $this->ticketService->changeStatus(
            $ticket,
            TicketStatus::from($request->status),
            $request->user()
        );

        return response()->json(['data' => new TicketResource($ticket)]);
    }

    public function addReply(AddReplyRequest $request, Ticket $ticket): JsonResponse
    {
        $this->authorizeView($request->user(), $ticket);

        $reply = $this->ticketService->addReply($ticket, $request->validated(), $request->user());

        return response()->json(['data' => new TicketReplyResource($reply)], 201);
    }

    public function activities(Request $request, Ticket $ticket): AnonymousResourceCollection
    {
        $this->authorizeView($request->user(), $ticket);

        $activities = $ticket->activities()
            ->with('user')
            ->orderBy('created_at')
            ->get();

        return TicketActivityResource::collection($activities);
    }

    private function authorizeView(mixed $user, Ticket $ticket): void
    {
        if ($user->role === UserRole::CUSTOMER && $ticket->user_id !== $user->id) {
            abort(403, 'Acesso negado.');
        }
    }

    private function authorizeModify(mixed $user, Ticket $ticket): void
    {
        if ($user->role === UserRole::CUSTOMER) {
            abort(403, 'Acesso negado.');
        }
    }
}
