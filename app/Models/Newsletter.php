<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Newsletter extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'subject',
        'preheader',
        'content',
        'plain_content',
        'template',
        'images',
        'thumbnail',
        'type',
        'target',
        'target_filters',
        'courses',
        'is_featured',
        'is_scheduled',
        'scheduled_at',
        'sent_at',
        'total_recipients',
        'sent_count',
        'opened_count',
        'clicked_count',
        'unsubscribed_count',
        'bounced_count',
        'open_rate',
        'click_rate',
        'created_by',
    ];

    protected $casts = [
        'template' => 'array',
        'images' => 'array',
        'target_filters' => 'array',
        'courses' => 'array',
        'is_featured' => 'boolean',
        'is_scheduled' => 'boolean',
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
        'total_recipients' => 'integer',
        'sent_count' => 'integer',
        'opened_count' => 'integer',
        'clicked_count' => 'integer',
        'unsubscribed_count' => 'integer',
        'bounced_count' => 'integer',
        'open_rate' => 'decimal:2',
        'click_rate' => 'decimal:2',
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

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeSent($query)
    {
        return $query->whereNotNull('sent_at');
    }

    public function scopeScheduled($query)
    {
        return $query->where('is_scheduled', true)->whereNull('sent_at');
    }

    public function scopeDraft($query)
    {
        return $query->whereNull('sent_at')->where('is_scheduled', false);
    }
}