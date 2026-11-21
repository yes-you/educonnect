<?php

namespace Database\Factories;

use App\Models\Opportunity;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Opportunity>
 */
class OpportunityFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->organization(),
            'type' => fake()->randomElement(Opportunity::TYPES),
            'title' => fake()->words(4, true),
            'description' => fake()->paragraph(),
            'location' => fake()->city(),
            'event_date' => fake()->dateTimeBetween('+1 week', '+3 months'),
            'deadline' => fake()->dateTimeBetween('+1 week', '+2 months'),
            'link' => 'https://example.com/'.fake()->slug(2),
            'status' => 'pending',
        ];
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'approved']);
    }
}
