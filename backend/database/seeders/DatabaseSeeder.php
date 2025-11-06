<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create a single test user
        User::factory()->create([
            'name' => 'Test User',
            'username' => 'testuser', // ✅ if you have username field
            'email' => 'test@example.com',
        ]);

        // Run additional seeders
        $this->call(UserSeeder::class);
    }
}
