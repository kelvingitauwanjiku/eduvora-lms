<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class KnowledgeBaseTopic extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'knowledge_base_topics';

    protected $fillable = [
        'uuid',
        'knowledge_base_id',
        'parent_id',
        'title',
        'slug',
        'description',
        'content',
        'attachments',
        'icon',
        'sort_order',
        'is_featured',
        'is_active',
        'is_published',
        'allow_comments',
        'views_count',
        'helpful_count',
        'not_helpful_count',
        'metadata',
    ];

    protected $casts = [
        'attachments' => 'array',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'is_published' => 'boolean',
        'allow_comments' => 'boolean',
        'sort_order' => 'integer',
        'views_count' => 'integer',
        'helpful_count' => 'integer',
        'not_helpful_count' => 'integer',
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

    public function knowledgeBase(): BelongsTo
    {
        return $this->belongsTo(KnowledgeBase::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(KnowledgeBaseTopic::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(KnowledgeBaseTopic::class, 'parent_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->where('is_published', true);
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }
}