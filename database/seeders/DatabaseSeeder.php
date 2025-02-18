<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Post;
use App\Models\PostVersion;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'role' => 'admin'
        ]);

        User::factory()->create([
            'name' => 'Editor User',
            'email' => 'editor@test.com',
            'role' => 'editor'
        ]);

        User::factory()->create([
            'name' => 'Author User',
            'email' => 'author@test.com',
            'role' => 'author'
        ]);

        Post::factory(10)->create();

        PostVersion::factory(5)->create();
    }
}
