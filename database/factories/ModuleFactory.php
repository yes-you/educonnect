<?php

namespace Database\Factories;

use App\Models\Module;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Module>
 */
class ModuleFactory extends Factory
{
    public function definition(): array
    {
        $subject = fake()->randomElement(['Mathematics', 'Science', 'English', 'Filipino', 'Araling Panlipunan']);

        return [
            'teacher_id' => User::factory()->teacher(),
            'title' => $subject.' '.fake()->words(2, true),
            'subject' => $subject,
            'grade_level' => fake()->randomElement(User::GRADE_LEVELS),
            'type' => fake()->randomElement(Module::TYPES),
            'description' => fake()->sentence(),
            'file_path' => 'modules/sample.pdf',
            'original_name' => 'sample.pdf',
        ];
    }
}
