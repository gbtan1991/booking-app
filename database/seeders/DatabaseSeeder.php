<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Admin account
        User::factory()->create([
            'name'     => 'Admin',
            'email'    => 'admin@swissbook.test',
            'password' => bcrypt('password'),
            'role'     => 'admin',
        ]);

        // Demo customer account
        User::factory()->create([
            'name'     => 'Demo Customer',
            'email'    => 'customer@swissbook.test',
            'password' => bcrypt('password'),
            'role'     => 'customer',
        ]);
    }
}
