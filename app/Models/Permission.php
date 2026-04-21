<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Permission extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'group',
        'guard_name',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'permission_role');
    }

    public function scopeGroup($query, $group)
    {
        return $query->where('group', $group);
    }

    public function scopeByGuard($query, $guard)
    {
        return $query->where('guard_name', $guard);
    }
}
