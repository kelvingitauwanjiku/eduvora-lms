<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LanguagePhrase extends Model
{
    use HasFactory;

    protected $fillable = [
        'language_id',
        'group',
        'key',
        'value',
        'default_value',
        'is_translated',
    ];

    protected $casts = [
        'is_translated' => 'boolean',
    ];

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }

    public function scopeGroup($query, $group)
    {
        return $query->where('group', $group);
    }

    public function scopeUntranslated($query)
    {
        return $query->where('is_translated', false);
    }
}
