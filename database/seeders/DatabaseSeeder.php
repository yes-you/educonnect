<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database: the six accounts first, then demo content that uses them.
     */
    public function run(): void
    {
        $this->call(UserSeeder::class);
        $this->call(DemoContentSeeder::class);
    }
}
