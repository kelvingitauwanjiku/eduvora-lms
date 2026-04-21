<?php

namespace App\Models;

use App\Models\Course\Course;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class BundleCourse extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'bundle_courses';

    protected $fillable = [
        'bundle_id',
        'course_id',
        'sort_order',
        'is_required',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_required' => 'boolean',
    ];

    public function bundle(): BelongsTo
    {
        return $this->belongsTo(Bundle::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}