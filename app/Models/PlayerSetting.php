<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class PlayerSetting extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'name',
        'slug',
        'description',
        'config',
        'auto_play',
        'show_controls',
        'enable_download',
        'enable_fullscreen',
        'enable_pip',
        'enable_playback_speed',
        'enable_quality_selector',
        'enable_subtitles',
        'enable_keyboard_shortcuts',
        'enable_watch_history',
        'default_playback_speed',
        'default_quality',
        'player_color',
        'logo_url',
        'logo_position',
        'is_active',
        'is_default',
        'sort_order',
    ];

    protected $casts = [
        'config' => 'array',
        'auto_play' => 'boolean',
        'show_controls' => 'boolean',
        'enable_download' => 'boolean',
        'enable_fullscreen' => 'boolean',
        'enable_pip' => 'boolean',
        'enable_playback_speed' => 'boolean',
        'enable_quality_selector' => 'boolean',
        'enable_subtitles' => 'boolean',
        'enable_keyboard_shortcuts' => 'boolean',
        'enable_watch_history' => 'boolean',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'sort_order' => 'integer',
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

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }
}