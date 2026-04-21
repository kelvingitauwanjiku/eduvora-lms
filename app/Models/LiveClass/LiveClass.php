<?php

namespace App\Models\LiveClass;

use App\Models\Course\Course;
use App\Models\Course\Section;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class LiveClass extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'user_id',
        'course_id',
        'section_id',
        'title',
        'description',
        'meeting_provider',
        'meeting_id',
        'meeting_password',
        'join_url',
        'host_url',
        'scheduled_at',
        'duration',
        'buffer_time',
        'max_participants',
        'enrolled_count',
        'attended_count',
        'price',
        'is_free',
        'is_recurring',
        'recurrence_pattern',
        'is_automatic_recording',
        'allow_chat',
        'allow_screen_share',
        'allow_recording',
        'is_published',
        'is_started',
        'is_ended',
        'is_cancelled',
        'cancellation_reason',
        'recording_url',
        'recording_duration',
        'metadata',
    ];

    protected $casts = [
        'recurrence_pattern' => 'array',
        'metadata' => 'array',
        'scheduled_at' => 'datetime',
        'price' => 'decimal:2',
        'is_free' => 'boolean',
        'is_recurring' => 'boolean',
        'is_automatic_recording' => 'boolean',
        'allow_chat' => 'boolean',
        'allow_screen_share' => 'boolean',
        'allow_recording' => 'boolean',
        'is_published' => 'boolean',
        'is_started' => 'boolean',
        'is_ended' => 'boolean',
        'is_cancelled' => 'boolean',
        'duration' => 'integer',
        'buffer_time' => 'integer',
        'max_participants' => 'integer',
        'enrolled_count' => 'integer',
        'attended_count' => 'integer',
        'recording_duration' => 'integer',
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function participants()
    {
        return $this->hasMany(LiveClassParticipant::class);
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('scheduled_at', '>', now())
            ->where('is_cancelled', false);
    }
}
