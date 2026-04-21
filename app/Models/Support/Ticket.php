<?php

namespace App\Models;

use App\Models\Support\TicketCategory;
use App\Models\Support\TicketMessage;
use App\Models\Support\TicketPriority;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'ticket_number',
        'user_id',
        'assigned_to',
        'category_id',
        'priority_id',
        'status_id',
        'subject',
        'description',
        'attachments',
        'type',
        'source',
        'course_id',
        'order_id',
        'is_locked',
        'is_starred',
        'is_pinned',
        'views_count',
        'responses_count',
        'status',
        'first_response_at',
        'resolved_at',
        'closed_at',
        'last_reply_at',
        'response_time',
        'resolution_time',
    ];

    protected $casts = [
        'is_locked' => 'boolean',
        'is_starred' => 'boolean',
        'is_pinned' => 'boolean',
        'attachments' => 'array',
        'response_time' => 'float',
        'resolution_time' => 'float',
        'first_response_at' => 'datetime',
        'resolved_at' => 'datetime',
        'closed_at' => 'datetime',
        'last_reply_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(TicketCategory::class);
    }

    public function priority(): BelongsTo
    {
        return $this->belongsTo(TicketPriority::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(TicketMessage::class);
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }
}
