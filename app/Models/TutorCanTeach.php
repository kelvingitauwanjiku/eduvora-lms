<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TutorCanTeach extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tutor_can_teach';

    protected $fillable = [
        'tutor_id',
        'category_id',
        'subject_id',
        'description',
        'thumbnail',
        'price',
        'currency',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function tutor(): BelongsTo
    {
        return $this->belongsTo(Tutor::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(TutorCategory::class, 'category_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(TutorSubject::class, 'subject_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}