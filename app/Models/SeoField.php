<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SeoField extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid', 'course_id', 'blog_id', 'bootcamp_id', 'ebook_id', 'page_id',
        'route', 'name_route', 'meta_title', 'meta_keywords', 'meta_description',
        'meta_robot', 'canonical_url', 'custom_url', 'json_ld', 'og_title',
        'og_description', 'og_image', 'og_type', 'og_url', 'twitter_card',
        'twitter_title', 'twitter_description', 'twitter_image', 'is_index', 'is_follow',
    ];

    protected $casts = ['is_index' => 'boolean', 'is_follow' => 'boolean'];
}
