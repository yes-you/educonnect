<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     * Demo content is added in Phase 10.
     */
    public function run(): void
    {
        $this->call(UserSeeder::class);
    }
}
