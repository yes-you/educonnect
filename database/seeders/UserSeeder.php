<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            ['Admin User', 'admin@educonnect.test', 'admin', null],
            ['Teacher User', 'teacher@educonnect.test', 'teacher', null],
            ['Student One', 'student@educonnect.test', 'student', 'Grade 10'],
            ['Student Two', 'student2@educonnect.test', 'student', 'Grade 10'],
            ['Parent User', 'parent@educonnect.test', 'parent', null],
            ['Organization User', 'org@educonnect.test', 'organization', null],
        ];

        foreach ($accounts as [$name, $email, $role, $gradeLevel]) {
            User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make('password'),
                'role' => $role,
                'grade_level' => $gradeLevel,
            ]);
        }

        // parent@ is the parent of student@ only.
        $parent = User::where('email', 'parent@educonnect.test')->first();
        $student = User::where('email', 'student@educonnect.test')->first();
        $parent->children()->attach($student->id);
    }
}
