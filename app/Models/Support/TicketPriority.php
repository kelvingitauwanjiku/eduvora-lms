<?php

namespace App\Models\Support;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketPriority extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'icon', 'color', 'sort_order', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];
}
