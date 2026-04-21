<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class WorkspaceInvite extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'workspace_invites';

    protected $fillable = [
        'uuid',
        'workspace_id',
        'invited_by',
        'email',
        'role',
        'token',
        'message',
        'status',
        'expires_at',
        'accepted_at',
    ];

    protected $casts = [
        'status' => 'string',
        'expires_at' => 'datetime',
        'accepted_at' => 'datetime',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = Str::uuid()->toString();
            }
            if (empty($model->token)) {
                $model->token = Str::random(64);
            }
        });
    }

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function invitedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired')->orWhere('expires_at', '<', now());
    }
}