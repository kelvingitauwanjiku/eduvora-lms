<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class NewsletterSubscriber extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'newsletter_subscribers';

    protected $fillable = [
        'uuid',
        'email',
        'name',
        'token',
        'is_verified',
        'is_active',
        'is_subscribed',
        'source',
        'preferences',
        'ip_address',
        'user_agent',
        'subscribed_at',
        'unsubscribed_at',
        'last_email_at',
        'emails_received',
        'emails_opened',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'is_active' => 'boolean',
        'is_subscribed' => 'boolean',
        'preferences' => 'array',
        'subscribed_at' => 'datetime',
        'unsubscribed_at' => 'datetime',
        'last_email_at' => 'datetime',
        'emails_received' => 'integer',
        'emails_opened' => 'integer',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = Str::uuid()->toString();
            }
            if (empty($model->token)) {
                $model->token = Str::random(100);
            }
        });
    }

    public function scopeSubscribed($query)
    {
        return $query->where('is_subscribed', true)->where('is_active', true);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }
}