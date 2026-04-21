<?php

namespace App\Models\Bootcamp;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class BootcampModule extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'bootcamp_id',
        'title',
        'description',
        'sort_order',
        'start_date',
        'end_date',
        'lessons_count',
        'duration_days',
        'restrictions',
        'metadata',
    ];

    protected $casts = [
        'restrictions' => 'array',
        'metadata' => 'array',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'sort_order' => 'integer',
        'lessons_count' => 'integer',
        'duration_days' => 'integer',
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

    public function liveClasses()
    {
        return $this->hasMany(BootcampLiveClass::class, 'module_id');
    }

    public function resources()
    {
        return $this->hasMany(BootcampResource::class, 'module_id');
    }
}
