<?php

namespace Database\Factories;

use App\Models\StudyGroup;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StudyGroup>
 */
class StudyGroupFactory extends Factory
{
    public function definition(): array
    {
        $subject = fake()->randomElement(['Mathematics', 'Science', 'English']);

        return [
            'owner_id' => User::factory(),
            'name' => $subject.' study circle',
            'subject' => $subject,
            'description' => fake()->sentence(),
        ];
    }
}
