<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Blog\Blog;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Blog>
 */
class BlogFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->randomElement([
            'How to Master',
            'The Complete Guide to',
            'Best Practices for',
            'Getting Started with',
            'Advanced Tips for',
        ]) . ' ' . fake()->randomElement([
            'Web Development',
            'JavaScript',
            'React',
            'Python',
            'Machine Learning',
            'Data Science',
            'Career Growth',
            'Remote Work',
        ]);

        return [
            'uuid' => Str::uuid()->toString(),
            'user_id' => User::factory(),
            'category_id' => null,
            'title' => $title,
            'slug' => Str::slug($title),
            'short_description' => fake()->sentence(15),
            'description' => fake()->optional()->paragraph(4),
            'content' => fake()->optional()->paragraphs(5),
            'thumbnail' => 'https://picsum.photos/seed/' . fake()->uuid() . '/1200/630',
            'banner' => 'https://picsum.photos/seed/' . fake()->uuid() . '/1920/600',
            'video_url' => fake()->optional()->url(),
            'is_paid' => fake()->boolean(20),
            'price' => fake()->randomFloat(2, 5, 29),
            'is_featured' => fake()->boolean(20),
            'meta_title' => $title,
            'meta_description' => fake()->sentence(10),
            'meta_keywords' => implode(', ', fake()->words(5)),
            'status' => 'published',
            'type' => fake()->randomElement(['article', 'video', 'news', 'tutorial']),
            'average_rating' => fake()->randomFloat(2, 4.0, 5.0),
            'ratings_count' => 0,
            'views_count' => 0,
            'shares_count' => 0,
            'comments_count' => 0,
            'likes_count' => 0,
            'bookmarks_count' => 0,
            'published_at' => now(),
        ];
    }

    public function article(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'article',
            'video_url' => null,
        ]);
    }

    public function video(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'video',
            'video_url' => 'https://www.youtube.com/watch?v=' . Str::random(11),
        ]);
    }

    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
        ]);
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
            'published_at' => null,
        ]);
    }
}