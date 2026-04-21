<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->randomElement([
            'Web Development',
            'Mobile Development',
            'Data Science',
            'Machine Learning',
            'DevOps',
            'Cyber Security',
            'UI/UX Design',
            'Digital Marketing',
            'Business',
            'Personal Development',
            'Finance',
            'Health & Fitness',
            'Music',
            'Photography',
            'Art & Crafts'
        ]);

        return [
            'uuid' => Str::uuid()->toString(),
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => fake()->optional()->sentence(15),
            'icon' => 'fa-' . Str::slug($name),
            'image' => 'https://picsum.photos/seed/' . fake()->uuid() . '/800/600',
            'parent_id' => null,
            'sort_order' => fake()->numberBetween(1, 100),
            'courses_count' => 0,
            'is_featured' => fake()->boolean(30),
            'is_active' => true,
        ];
    }

    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}