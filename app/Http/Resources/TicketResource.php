<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'ticket_number' => $this->ticket_number,
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'status_color' => $this->status->color(),
            'priority' => $this->priority->value,
            'priority_label' => $this->priority->label(),
            'priority_color' => $this->priority->color(),
            'due_date' => $this->due_date?->toISOString(),
            'assigned_at' => $this->assigned_at?->toISOString(),
            'resolved_at' => $this->resolved_at?->toISOString(),
            'closed_at' => $this->closed_at?->toISOString(),
            'response_time' => $this->response_time,
            'resolution_time' => $this->resolution_time,
            'replies_count' => $this->whenCounted('replies'),
            'user' => $this->whenLoaded('user', fn () => new UserResource($this->user)),
            'technician' => $this->whenLoaded('technician', fn () => $this->technician ? new UserResource($this->technician) : null),
            'category' => $this->whenLoaded('category', fn () => new CategoryResource($this->category)),
            'replies' => $this->whenLoaded('replies', fn () => TicketReplyResource::collection($this->replies)),
            'attachments' => $this->whenLoaded('attachments', fn () => AttachmentResource::collection($this->attachments)),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
