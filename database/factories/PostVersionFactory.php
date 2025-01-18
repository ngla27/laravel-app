<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostVersionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'post_id' => Post::inRandomOrder()->first()->id,
            'title' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'meta_title' => fake()->sentence(),
            'meta_description' => fake()->sentence(),
            'keywords' => fake()->word(),
            'edited_by' => User::where('role', 'editor')
                            ->orWhere('role', 'author')
                            ->inRandomOrder()->first()
                            ->email,
            'updated_at' => fake()->dateTimeBetween('-10 months', 'now')->getTimestamp(),
            'start_timestamp' => fake()->dateTimeBetween('-3 months', 'now')->getTimestamp()
        ];
    }
}
