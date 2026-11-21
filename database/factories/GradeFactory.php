<?php

namespace Database\Factories;

use App\Models\Grade;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Grade>
 */
class GradeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'student_id' => User::factory(),
            'teacher_id' => User::factory()->teacher(),
            'subject' => fake()->randomElement(['Mathematics', 'Science', 'English', 'Filipino']),
            'quarter' => fake()->randomElement(Grade::QUARTERS),
            'grade' => fake()->numberBetween(75, 98),
            'remarks' => fake()->optional()->sentence(4),
        ];
    }
}
