<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Media extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid', 'user_id', 'folder', 'file_name', 'original_name', 'mime_type',
        'extension', 'size', 'url', 'thumbnail', 'path', 'width', 'height',
        'duration', 'variations', 'metadata', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'variations' => 'array',
        'metadata' => 'array',
    ];
}
