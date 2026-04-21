<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use App\Models\Course\Course;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Course>
 */
class CourseFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->randomElement([
            'Complete ',
            'Master ',
            'Advanced ',
            'The Complete '
        ]) . fake()->randomElement([
            'Web Development',
            'JavaScript',
            'React',
            'Python',
            'Laravel',
            'Data Science',
            'Machine Learning',
            'iOS Development',
            'Android Development',
            'DevOps'
        ]) . ' Bootcamp';

        return [
            'uuid' => Str::uuid()->toString(),
            'user_id' => User::factory(),
            'category_id' => Category::factory(),
            'title' => $title,
            'slug' => Str::slug($title),
            'short_description' => fake()->sentence(20),
            'description' => fake()->optional()->paragraph(5),
            'description_diagram' => fake()->optional()->paragraph(3),
            'thumbnail' => 'https://picsum.photos/seed/' . fake()->uuid() . '/800/450',
            'preview_video' => 'https://www.youtube.com/watch?v=dQw4w9Wg6cQ',
            'preview_thumbnail' => 'https://picsum.photos/seed/' . fake()->uuid() . '/400/225',
            'video_preload' => 'metadata',
            'is_paid' => fake()->boolean(70),
            'price' => fake()->randomElement([0, 19.99, 29.99, 49.99, 99.99, 149.99]),
            'discount_price' => null,
            'discount_enabled' => false,
            'discount_start_at' => null,
            'discount_end_at' => null,
            'currency' => 'USD',
            'level' => fake()->randomElement(['beginner', 'intermediate', 'expert']),
            'language' => 'English',
            'subtitle' => 'English',
            'duration' => fake()->numberBetween(1, 60),
            'duration_text' => fake()->randomElement(['1 hour', '2 hours', '5 hours', '10 hours', '20 hours', '40 hours']),
            'is_publish' => true,
            'is_featured' => fake()->boolean(20),
            'is_rating' => fake()->boolean(60),
            'average_rating' => fake()->randomFloat(2, 3.5, 5),
            'ratings_count' => 0,
            'reviews_count' => 0,
            'students_count' => 0,
            'lessons_count' => 0,
            'lessons_duration' => 0,
            'sections_count' => 0,
            'total_income' => 0,
            'admin_commission_rate' => 30,
            'is_certificate' => true,
            'certificate_enabled' => true,
            'certificate_title' => 'Certificate of Completion',
            'course_category' => fake()->randomElement(['free', 'paid', 'subscription']),
            'has_drip_content' => false,
            'drip_content_days' => 1,
            'is_archived' => false,
            'status' => 'published',
            'published_at' => now(),
        ];
    }

    public function free(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_paid' => false,
            'price' => 0,
        ]);
    }

    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
        ]);
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'published',
            'published_at' => now(),
        ]);
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
            'is_publish' => false,
            'published_at' => null,
        ]);
    }
}