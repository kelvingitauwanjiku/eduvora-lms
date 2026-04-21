<?php

namespace App\Models\Support;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid', 'ticket_id', 'user_id', 'sender_id', 'receiver_id', 'message',
        'attachments', 'is_internal', 'is_note', 'is_starred', 'is_edited',
        'edited_at', 'is_read', 'read_at', 'metadata',
    ];

    protected $casts = [
        'is_internal' => 'boolean', 'is_note' => 'boolean', 'is_starred' => 'boolean',
        'is_edited' => 'boolean', 'is_read' => 'boolean', 'attachments' => 'array',
        'metadata' => 'array', 'edited_at' => 'datetime', 'read_at' => 'datetime',
    ];
}
