<?php

namespace App\Models\LiveClass;

use App\Models\Course\Enrollment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LiveClassParticipant extends Model
{
    use HasFactory;

    protected $fillable = [
        'live_class_id',
        'user_id',
        'enrollment_id',
        'registered_at',
        'joined_at',
        'left_at',
        'attendance_duration',
        'is_present',
        'is_host',
        'is_co_host',
        'status',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'registered_at' => 'datetime',
        'joined_at' => 'datetime',
        'left_at' => 'datetime',
        'attendance_duration' => 'integer',
        'is_present' => 'boolean',
        'is_host' => 'boolean',
        'is_co_host' => 'boolean',
    ];

    public function liveClass()
    {
        return $this->belongsTo(LiveClass::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class);
    }
}
