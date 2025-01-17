<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'meta_title' => fake()->sentence(),
            'meta_description' => fake()->sentence(),
            'keywords' => fake()->word(),
            'last_edited' => fake()->dateTimeBetween('now', '+6 months')->getTimestamp(),
            'updated_by' => fake()->email(),
            'published_on' => fake()->dateTimeBetween('now', '+6 months')->getTimestamp()
        ];
    }
}
