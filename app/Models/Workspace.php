<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Workspace extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'name',
        'slug',
        'description',
        'icon',
        'cover_image',
        'owner_id',
        'timezone',
        'visibility',
        'allow_guest',
        'settings',
        'members_count',
        'pages_count',
        'files_count',
        'storage_used',
        'storage_limit',
        'is_active',
        'is_featured',
    ];

    protected $casts = [
        'settings' => 'array',
        'storage_used' => 'integer',
        'storage_limit' => 'integer',
        'allow_guest' => 'boolean',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'members_count' => 'integer',
        'pages_count' => 'integer',
        'files_count' => 'integer',
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

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(WorkspaceMember::class, 'workspace_id');
    }

    public function pages(): HasMany
    {
        return $this->hasMany(WorkspacePage::class);
    }

    public function invites(): HasMany
    {
        return $this->hasMany(WorkspaceInvite::class);
    }

    public function scopeVisible($query)
    {
        return $query->whereIn('visibility', ['team', 'public']);
    }

    public function scopePrivate($query)
    {
        return $query->where('visibility', 'private');
    }
}