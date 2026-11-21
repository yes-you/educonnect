<?php

namespace Database\Factories;

use App\Models\Announcement;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Announcement>
 */
class AnnouncementFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->teacher(),
            'title' => fake()->sentence(4),
            'body' => fake()->paragraph(),
            'audience' => fake()->randomElement(Announcement::AUDIENCES),
        ];
    }
}
