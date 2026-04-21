<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FrontendSetting extends Model
{
    use HasFactory;

    protected $table = 'frontend_settings';

    protected $fillable = [
        'key',
        'value',
        'type',
        'is_translatable',
    ];

    protected $casts = [
        'is_translatable' => 'boolean',
    ];
}