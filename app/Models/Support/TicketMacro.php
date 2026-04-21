<?php

namespace App\Models\Support;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class TicketMacro extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'ticket_macros';

    protected $fillable = [
        'uuid',
        'name',
        'slug',
        'description',
        'message_template',
        'actions',
        'attachments',
        'category_id',
        'status_id',
        'priority_id',
        'is_featured',
        'is_active',
        'usage_count',
        'created_by',
    ];

    protected $casts = [
        'actions' => 'array',
        'attachments' => 'array',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'usage_count' => 'integer',
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

    public function category(): BelongsTo
    {
        return $this->belongsTo(TicketCategory::class, 'category_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(TicketStatus::class, 'status_id');
    }

    public function priority(): BelongsTo
    {
        return $this->belongsTo(TicketPriority::class, 'priority_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }
}