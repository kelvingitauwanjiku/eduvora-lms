<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class WorkspaceBlock extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'workspace_blocks';

    protected $fillable = [
        'uuid',
        'page_id',
        'user_id',
        'type',
        'content',
        'properties',
        'parent_block_id',
        'sort_order',
        'is_deleted',
    ];

    protected $casts = [
        'content' => 'array',
        'properties' => 'array',
        'sort_order' => 'integer',
        'is_deleted' => 'boolean',
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

    public function page(): BelongsTo
    {
        return $this->belongsTo(WorkspacePage::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_deleted', false);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }
}