<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationSetting extends Model
{
    use HasFactory;

    protected $table = 'notification_settings';

    protected $fillable = [
        'uuid',
        'type',
        'name',
        'description',
        'icon',
        'category',
        'addon_identifier',
        'user_types',
        'system_notification',
        'email_notification',
        'push_notification',
        'sms_notification',
        'in_app_notification',
        'subject',
        'template',
        'shortcodes',
        'setting_title',
        'setting_sub_title',
        'is_editable',
        'is_active',
        'is_default',
    ];

    protected $casts = [
        'user_types' => 'array',
        'system_notification' => 'boolean',
        'email_notification' => 'boolean',
        'push_notification' => 'boolean',
        'sms_notification' => 'boolean',
        'in_app_notification' => 'boolean',
        'is_editable' => 'boolean',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}