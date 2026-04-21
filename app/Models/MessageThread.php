<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class MessageThread extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'thread_number',
        'course_id',
        'lesson_id',
        'sender_id',
        'receiver_id',
        'type',
        'subject',
        'last_message',
        'last_message_at',
        'messages_count',
        'participants',
        'is_archived',
        'is_starred',
        'is_pinned',
        'is_muted',
        'metadata',
    ];

    protected $casts = [
        'participants' => 'array',
        'is_archived' => 'boolean',
        'is_starred' => 'boolean',
        'is_pinned' => 'boolean',
        'is_muted' => 'boolean',
        'messages_count' => 'integer',
        'last_message_at' => 'datetime',
        'metadata' => 'array',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = Str::uuid()->toString();
            }
        });
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function scopeDirect($query)
    {
        return $query->where('type', 'direct');
    }

    public function scopeArchived($query)
    {
        return $query->where('is_archived', true);
    }

    public function scopePinned($query)
    {
        return $query->where('is_pinned', true);
    }
}