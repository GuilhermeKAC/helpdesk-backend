<?php

namespace App\Models;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'technician_id',
        'category_id',
        'title',
        'description',
        'status',
        'priority',
        'assigned_at',
        'resolved_at',
        'closed_at',
        'due_date',
        'response_time',
        'resolution_time',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'status' => TicketStatus::class,
            'priority' => TicketPriority::class,
            'assigned_at' => 'datetime',
            'resolved_at' => 'datetime',
            'closed_at' => 'datetime',
            'due_date' => 'datetime',
            'metadata' => 'array',
            'response_time' => 'integer',
            'resolution_time' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function technician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'technician_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function replies(): HasMany
    {
        return $this->hasMany(TicketReply::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(TicketActivity::class);
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    public function scopeOpen($query)
    {
        return $query->where('status', TicketStatus::OPEN);
    }

    public function scopeByStatus($query, TicketStatus $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByPriority($query, TicketPriority $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForTechnician($query, int $technicianId)
    {
        return $query->where('technician_id', $technicianId);
    }
}
