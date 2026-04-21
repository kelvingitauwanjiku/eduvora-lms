<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'icon' => $this->icon,
            'image' => $this->image,
            'is_featured' => $this->is_featured,
            'courses_count' => $this->courses_count,
            'sort_order' => $this->sort_order,
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}