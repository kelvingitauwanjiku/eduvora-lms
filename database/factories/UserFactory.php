<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'uuid' => Str::uuid()->toString(),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'username' => fake()->unique()->userName(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'phone' => fake()->phoneNumber(),
            'avatar' => 'https://api.dicebear.com/7.x/avataaars/svg?seed=' . fake()->uuid(),
            'cover_photo' => 'https://picsum.photos/seed/' . fake()->uuid() . '/1200/400',
            'bio' => fake()->optional()->sentence(20),
            'headline' => fake()->optional()->jobTitle(),
            'website' => fake()->optional()->url(),
            'facebook' => 'https://facebook.com/' . fake()->userName(),
            'twitter' => 'https://twitter.com/' . fake()->userName(),
            'linkedin' => 'https://linkedin.com/in/' . fake()->userName(),
            'youtube' => 'https://youtube.com/@' . fake()->userName(),
            'instagram' => 'https://instagram.com/' . fake()->userName(),
            'skills' => fake()->randomElements([
                'PHP', 'Laravel', 'JavaScript', 'React', 'Vue.js', 
                'Python', 'Node.js', 'MySQL', 'PostgreSQL', 'AWS'
            ], rand(3, 8)),
            'languages' => ['English', 'Spanish'],
            'address' => fake()->streetAddress(),
            'city' => fake()->city(),
            'state' => fake()->state(),
            'country' => 'United States',
            'postal_code' => fake()->postcode(),
            'gender' => fake()->randomElement(['male', 'female', 'other']),
            'date_of_birth' => fake()->dateOfBirth(18, 65),
            'status' => 'active',
            'is_verified' => fake()->boolean(30),
            'is_featured' => fake()->boolean(20),
            'is_instructor' => fake()->boolean(40),
            'is_affiliate' => fake()->boolean(20),
            'balance' => 0,
            'total_earnings' => 0,
            'total_spent' => 0,
            'courses_count' => 0,
            'students_count' => 0,
            'average_rating' => 0,
            'reviews_count' => 0,
            'online_status' => fake()->randomElement(['online', 'away', 'dnd', 'offline']),
            'last_seen_at' => fake()->dateTimeBetween('-1 hour', 'now'),
            'is_public_profile' => true,
            'status_message' => fake()->optional()->sentence(6),
            'referral_code' => Str::upper(Str::random(8)),
            'time_zone' => 'UTC',
            'remember_token' => Str::random(10),
        ];
    }

    public function instructor(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_instructor' => true,
            'headline' => fake()->randomElement([
                'Senior Software Engineer',
                'Full Stack Developer',
                'Data Scientist',
                'UX Designer',
                'DevOps Engineer'
            ]),
            'bio' => fake()->paragraph(3),
        ]);
    }

    public function verified(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_verified' => true,
        ]);
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
            'is_instructor' => true,
        ]);
    }
}