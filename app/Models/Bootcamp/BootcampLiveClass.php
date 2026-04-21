<?php

namespace App\Models\Bootcamp;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class BootcampLiveClass extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'bootcamp_id',
        'module_id',
        'instructor_id',
        'title',
        'description',
        'scheduled_at',
        'duration',
        'meeting_provider',
        'meeting_id',
        'meeting_password',
        'join_url',
        'sort_order',
        'is_mandatory',
        'is_recorded',
        'recording_url',
        'status',
        'joining_data',
        'force_stop',
    ];

    protected $casts = [
        'joining_data' => 'array',
        'scheduled_at' => 'datetime',
        'duration' => 'integer',
        'sort_order' => 'integer',
        'is_mandatory' => 'boolean',
        'is_recorded' => 'boolean',
        'force_stop' => 'boolean',
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

    public function bootcamp()
    {
        return $this->belongsTo(Bootcamp::class);
    }

    public function module()
    {
        return $this->belongsTo(BootcampModule::class, 'module_id');
    }

    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }
}
