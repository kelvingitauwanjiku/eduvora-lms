<?php

namespace Database\Seeders;

use App\Models\Course\Course;
use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $instructors = User::where('is_instructor', true)->get();
        $categories = Category::where('is_active', true)->get();
        
        if ($instructors->isEmpty() || $categories->isEmpty()) {
            $this->command->warn('Need instructors and categories first');
            return;
        }

        $courses = [
            [
                'title' => 'Complete Web Development Bootcamp',
                'slug' => 'complete-web-development-bootcamp',
                'short_description' => 'Learn HTML, CSS, JavaScript, React, Node.js and more in this comprehensive course',
                'description' => '<p>Master web development from scratch. This course covers everything you need to become a professional web developer.</p>',
                'level' => 'beginner',
                'price' => 49.99,
                'discount_price' => 19.99,
                'is_free' => false,
                'is_featured' => true,
            ],
            [
                'title' => 'Advanced React Patterns',
                'slug' => 'advanced-react-patterns',
                'short_description' => 'Master advanced React concepts and design patterns',
                'description' => '<p>Take your React skills to the next level with advanced patterns and best practices.</p>',
                'level' => 'advanced',
                'price' => 79.99,
                'discount_price' => 39.99,
                'is_free' => false,
                'is_featured' => true,
            ],
            [
                'title' => 'Python for Data Science',
                'slug' => 'python-data-science',
                'short_description' => 'Learn Python programming for data analysis and machine learning',
                'description' => '<p>Comprehensive Python course focused on data science and machine learning applications.</p>',
                'level' => 'intermediate',
                'price' => 69.99,
                'discount_price' => 29.99,
                'is_free' => false,
                'is_featured' => false,
            ],
            [
                'title' => 'UI/UX Design Fundamentals',
                'slug' => 'ui-ux-design-fundamentals',
                'short_description' => 'Learn the principles of great UI/UX design',
                'description' => '<p>Master the fundamentals of user interface and user experience design.</p>',
                'level' => 'beginner',
                'price' => 0,
                'discount_price' => 0,
                'is_free' => true,
                'is_featured' => false,
            ],
        ];

        foreach ($courses as $courseData) {
            $instructor = $instructors->random();
            $category = $categories->random();
            
            Course::updateOrCreate(
                ['slug' => $courseData['slug']],
                array_merge($courseData, [
                    'user_id' => $instructor->id,
                    'category_id' => $category->id,
                    'published_at' => now(),
                    'uuid' => \Illuminate\Support\Str::uuid()->toString(),
                ])
            );
        }

        $this->command->info('Created ' . count($courses) . ' courses');
    }
}