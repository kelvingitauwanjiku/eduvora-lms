<?php

namespace App\Models\Bootcamp;

use App\Models\Course\Lesson;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class BootcampResource extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'bootcamp_id',
        'module_id',
        'lesson_id',
        'title',
        'upload_type',
        'file',
        'file_url',
        'file_name',
        'file_size',
        'downloads_count',
        'is_preview',
        'is_downloadable',
        'uploaded_at',
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
        'file_size' => 'integer',
        'downloads_count' => 'integer',
        'is_preview' => 'boolean',
        'is_downloadable' => 'boolean',
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

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }
}
