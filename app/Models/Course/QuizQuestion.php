<?php

namespace App\Models\Course;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuizQuestion extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'quiz_id',
        'parent_id',
        'question',
        'explanation',
        'type',
        'options',
        'correct_answer',
        'correct_answers',
        'marks',
        'sort_order',
        'is_required',
        'is_active',
        'metadata',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_active' => 'boolean',
        'marks' => 'decimal:2',
        'options' => 'array',
        'correct_answers' => 'array',
        'metadata' => 'array',
    ];

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(QuizQuestion::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(QuizQuestion::class, 'parent_id');
    }

    public function getOptionsArrayAttribute(): array
    {
        return is_array($this->options) ? $this->options : json_decode($this->options, true) ?? [];
    }
}
