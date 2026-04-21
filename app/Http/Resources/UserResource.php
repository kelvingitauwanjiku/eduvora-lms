<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'name' => $this->first_name . ' ' . $this->last_name,
            'email' => $this->email,
            'username' => $this->username,
            'avatar' => $this->avatar,
            'cover_photo' => $this->cover_photo,
            'bio' => $this->bio,
            'headline' => $this->headline,
            'is_verified' => $this->is_verified,
            'is_instructor' => $this->is_instructor,
            'is_featured' => $this->is_featured,
            'average_rating' => $this->average_rating,
            'reviews_count' => $this->reviews_count,
            'students_count' => $this->students_count,
            'courses_count' => $this->courses_count,
            'online_status' => $this->online_status,
            'status_message' => $this->status_message,
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}