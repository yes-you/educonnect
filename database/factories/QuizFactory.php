<?php

namespace Database\Factories;

use App\Models\Quiz;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Quiz>
 */
class QuizFactory extends Factory
{
    public function definition(): array
    {
        $subject = fake()->randomElement(['Mathematics', 'Science', 'English']);

        return [
            'teacher_id' => User::factory()->teacher(),
            'module_id' => null,
            'title' => $subject.' practice quiz',
            'subject' => $subject,
            'grade_level' => fake()->randomElement(User::GRADE_LEVELS),
            'description' => fake()->sentence(),
            'is_published' => false,
        ];
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => ['is_published' => true]);
    }
}
