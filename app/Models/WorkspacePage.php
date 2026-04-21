<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class WorkspacePage extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'workspace_pages';

    protected $fillable = [
        'uuid',
        'workspace_id',
        'parent_id',
        'user_id',
        'title',
        'icon',
        'content',
        'cover',
        'properties',
        'schema',
        'is_favorite',
        'is_template',
        'is_published',
        'sort_order',
        'views_count',
    ];

    protected $casts = [
        'properties' => 'array',
        'schema' => 'array',
        'is_favorite' => 'boolean',
        'is_template' => 'boolean',
        'is_published' => 'boolean',
        'sort_order' => 'integer',
        'views_count' => 'integer',
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

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(WorkspacePage::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(WorkspacePage::class, 'parent_id');
    }

    public function blocks(): HasMany
    {
        return $this->hasMany(WorkspaceBlock::class, 'page_id');
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeTemplates($query)
    {
        return $query->where('is_template', true);
    }

    public function scopeRootPages($query)
    {
        return $query->whereNull('parent_id');
    }
}