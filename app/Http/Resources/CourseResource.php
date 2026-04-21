<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'title' => $this->title,
            'slug' => $this->slug,
            'short_description' => $this->short_description,
            'description' => $this->description,
            'thumbnail' => $this->thumbnail,
            'preview_video' => $this->preview_video,
            'is_paid' => $this->is_paid,
            'price' => $this->price,
            'discount_price' => $this->discount_price,
            'current_price' => $this->current_price ?? $this->price,
            'is_on_discount' => $this->is_on_discount ?? false,
            'level' => $this->level,
            'language' => $this->language,
            'duration' => $this->duration,
            'duration_text' => $this->duration_text,
            'is_featured' => $this->is_featured,
            'average_rating' => $this->average_rating,
            'ratings_count' => $this->ratings_count,
            'reviews_count' => $this->reviews_count,
            'students_count' => $this->students_count,
            'lessons_count' => $this->lessons_count,
            'sections_count' => $this->sections_count,
            'is_certificate' => $this->is_certificate,
            'status' => $this->status,
            'instructor' => new UserResource($this->whenLoaded('user')),
            'category' => new CategoryResource($this->whenLoaded('category')),
            'published_at' => $this->published_at?->toIso8601String(),
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}