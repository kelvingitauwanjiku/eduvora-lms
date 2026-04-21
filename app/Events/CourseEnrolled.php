<?php

namespace App\Events;

use App\Models\User;
use App\Models\Course\Course;
use App\Models\Course\Enrollment;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CourseEnrolled
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Enrollment $enrollment,
        public Course $course,
        public User $user
    ) {}
}