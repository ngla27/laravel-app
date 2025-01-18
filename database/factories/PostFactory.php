<?php

namespace Database\Factories;

use App\Models\User;
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
            'status' => fake()->randomElement(['draft', 'published', 'archived']),
            'authored_by' => User::where('role', 'author')
                            ->inRandomOrder()->first()
                            ->email,
        ];
    }
}
