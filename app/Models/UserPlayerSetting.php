<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPlayerSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'course_id',
        'autoplay',
        'playback_speed',
        'quality',
        'subtitles',
        'subtitles_size',
        'volume',
        'muted',
        'loop',
        'show_transcript',
        'show_chapters',
        'keyboard_shortcuts',
        'continue_watching',
        'skip_intro',
        'skip_outro',
        'play_next',
        'remember_position',
        'mini_player',
        'theater_mode',
        'picture_in_picture',
        'download_enabled',
        'analytics_enabled',
        'last_position',
        'completed_lessons',
    ];

    protected $casts = [
        'autoplay' => 'boolean',
        'playback_speed' => 'decimal:1',
        'subtitles_size' => 'integer',
        'volume' => 'integer',
        'muted' => 'boolean',
        'loop' => 'boolean',
        'show_transcript' => 'boolean',
        'show_chapters' => 'boolean',
        'keyboard_shortcuts' => 'boolean',
        'continue_watching' => 'boolean',
        'skip_intro' => 'integer',
        'skip_outro' => 'integer',
        'play_next' => 'boolean',
        'remember_position' => 'boolean',
        'mini_player' => 'boolean',
        'theater_mode' => 'boolean',
        'picture_in_picture' => 'boolean',
        'download_enabled' => 'boolean',
        'analytics_enabled' => 'boolean',
        'last_position' => 'integer',
        'completed_lessons' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course\Course::class);
    }

    public function scopeForCourse($query, int $courseId)
    {
        return $query->where('course_id', $courseId);
    }

    public function scopeGlobal($query)
    {
        return $query->whereNull('course_id');
    }
}