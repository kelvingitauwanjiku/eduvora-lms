<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Web Development', 'slug' => 'web-development', 'icon' => 'fa-code', 'is_featured' => true],
            ['name' => 'Mobile Development', 'slug' => 'mobile-development', 'icon' => 'fa-mobile', 'is_featured' => true],
            ['name' => 'Data Science', 'slug' => 'data-science', 'icon' => 'fa-database', 'is_featured' => true],
            ['name' => 'Machine Learning', 'slug' => 'machine-learning', 'icon' => 'fa-brain', 'is_featured' => true],
            ['name' => 'DevOps', 'slug' => 'devops', 'icon' => 'fa-cloud', 'is_featured' => true],
            ['name' => 'Cyber Security', 'slug' => 'cyber-security', 'icon' => 'fa-shield', 'is_featured' => true],
            ['name' => 'UI/UX Design', 'slug' => 'ui-ux-design', 'icon' => 'fa-palette', 'is_featured' => true],
            ['name' => 'Digital Marketing', 'slug' => 'digital-marketing', 'icon' => 'fa-bullhorn', 'is_featured' => false],
            ['name' => 'Business', 'slug' => 'business', 'icon' => 'fa-briefcase', 'is_featured' => false],
            ['name' => 'Personal Development', 'slug' => 'personal-development', 'icon' => 'fa-user', 'is_featured' => false],
            ['name' => 'Finance', 'slug' => 'finance', 'icon' => 'fa-dollar', 'is_featured' => false],
            ['name' => 'Health & Fitness', 'slug' => 'health-fitness', 'icon' => 'fa-heart', 'is_featured' => false],
            ['name' => 'Music', 'slug' => 'music', 'icon' => 'fa-music', 'is_featured' => false],
            ['name' => 'Photography', 'slug' => 'photography', 'icon' => 'fa-camera', 'is_featured' => false],
            ['name' => 'Art & Crafts', 'slug' => 'art-crafts', 'icon' => 'fa-paint-brush', 'is_featured' => false],
        ];

        foreach ($categories as $index => $category) {
            Category::updateOrCreate(
                ['slug' => $category['slug']],
                array_merge($category, [
                    'uuid' => \Illuminate\Support\Str::uuid()->toString(),
                    'description' => 'Learn ' . $category['name'] . ' from scratch',
                    'image' => 'https://picsum.photos/seed/' . $category['slug'] . '/800/600',
                    'sort_order' => $index + 1,
                    'is_active' => true,
                    'courses_count' => rand(10, 100),
                ])
            );
        }
    }
}