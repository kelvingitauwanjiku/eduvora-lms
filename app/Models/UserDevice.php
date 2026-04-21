<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDevice extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'device_id',
        'ip_address',
        'user_agent',
        'platform',
        'browser',
        'device_type',
        'location',
        'is_current',
        'is_banned',
        'last_active_at',
    ];

    protected $casts = [
        'location' => 'array',
        'is_current' => 'boolean',
        'is_banned' => 'boolean',
        'last_active_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }

    public function scopeActive($query)
    {
        return $query->where('last_active_at', '>=', now()->subDays(30));
    }

    public function ban(): void
    {
        $this->update(['is_banned' => true]);
    }

    public function unban(): void
    {
        $this->update(['is_banned' => false]);
    }
}