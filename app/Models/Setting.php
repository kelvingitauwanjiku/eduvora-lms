<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'group',
        'name',
        'display_name',
        'description',
        'type',
        'value',
        'default_value',
        'options',
        'validation_rules',
        'is_translatable',
        'is_encrypted',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'options' => 'array',
        'validation_rules' => 'array',
        'is_translatable' => 'boolean',
        'is_encrypted' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeGroup($query, string $group)
    {
        return $query->where('group', $group);
    }
}