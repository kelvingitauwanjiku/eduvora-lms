<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Blog\Blog;
use Illuminate\Database\Seeder;

class BlogSeeder extends Seeder
{
    public function run(): void
    {
        $posts = [
            ['title' => 'How to Get Started with Web Development', 'type' => 'article'],
            ['title' => 'Best Practices for React Developers', 'type' => 'article'],
            ['title' => 'Python vs JavaScript - Which to Learn First', 'type' => 'article'],
            ['title' => 'Building Your First Machine Learning Model', 'type' => 'tutorial'],
            ['title' => 'Remote Work Tips for Developers', 'type' => 'article'],
            ['title' => 'How to Crack Coding Interviews', 'type' => 'video'],
            ['title' => 'UI Design Principles Every Developer Should Know', 'type' => 'article'],
            ['title' => 'The Future of AI in Tech', 'type' => 'article'],
        ];

        $instructors = User::where('is_instructor', true)->get();

        foreach ($posts as $post) {
            $instructor = $instructors->random();

            Blog::create([
                'uuid' => \Illuminate\Support\Str::uuid()->toString(),
                'user_id' => $instructor->id,
                'title' => $post['title'],
                'slug' => \Illuminate\Support\Str::slug($post['title']),
                'short_description' => 'Learn about ' . strtolower($post['title']),
                'description' => 'This is a comprehensive guide on ' . strtolower($post['title']) . '.',
                'content' => 'Full content of the article goes here...',
                'thumbnail' => 'https://picsum.photos/seed/' . \Illuminate\Support\Str::slug($post['title']) . '/1200/630',
                'type' => $post['type'],
                'is_featured' => rand(1, 10) <= 2,
                'status' => 'published',
                'views_count' => rand(100, 5000),
                'published_at' => now(),
            ]);
        }
    }
}