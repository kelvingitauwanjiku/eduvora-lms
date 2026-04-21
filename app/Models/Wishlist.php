<?php

namespace App\Models;

use App\Models\Bootcamp\Bootcamp;
use App\Models\Course\Course;
use App\Models\Ebook\Ebook;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Wishlist extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'course_id',
        'ebook_id',
        'bootcamp_id',
        'item_type',
        'notes',
        'priority',
    ];

    protected $casts = [
        'priority' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function ebook(): BelongsTo
    {
        return $this->belongsTo(Ebook::class);
    }

    public function bootcamp(): BelongsTo
    {
        return $this->belongsTo(Bootcamp::class);
    }

    public function scopeForCourse($query)
    {
        return $query->where('item_type', 'course');
    }

    public function scopeForEbook($query)
    {
        return $query->where('item_type', 'ebook');
    }
}
