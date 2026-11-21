<?php

namespace Tests\Feature;

use App\Models\Grade;
use App\Models\Module;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditFixesTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_with_grades_cannot_delete_own_account(): void
    {
        $teacher = User::factory()->teacher()->create();
        Grade::factory()->create(['teacher_id' => $teacher->id]);

        $this->actingAs($teacher)
            ->delete(route('profile.destroy'), ['password' => 'password'])
            ->assertSessionHasErrorsIn('userDeletion', 'password');

        $this->assertModelExists($teacher);
        $this->assertDatabaseCount('grades', 1);
    }

    public function test_questions_are_locked_once_a_student_took_the_quiz(): void
    {
        $teacher = User::factory()->teacher()->create();
        $quiz = Quiz::factory()->published()->create(['teacher_id' => $teacher->id, 'grade_level' => 'Grade 10']);
        $question = Question::factory()->create(['quiz_id' => $quiz->id]);
        QuizAttempt::create([
            'quiz_id' => $quiz->id,
            'user_id' => User::factory()->create()->id,
            'score' => 1,
            'total' => 1,
            'answers' => [$question->id => 'a'],
        ]);

        $this->actingAs($teacher)->delete(route('questions.destroy', $question))->assertSessionHas('error');
        $this->assertModelExists($question);

        $this->actingAs($teacher)->post(route('questions.store', $quiz), [
            'question' => 'New?', 'option_a' => 'a', 'option_b' => 'b', 'option_c' => 'c', 'option_d' => 'd',
            'correct_option' => 'a',
        ])->assertSessionHas('error');
        $this->assertDatabaseCount('questions', 1);
    }

    public function test_student_of_another_grade_cannot_open_or_download_a_module(): void
    {
        $student = User::factory()->create(['grade_level' => 'Grade 10']);
        $module = Module::factory()->create(['grade_level' => 'Grade 7']);

        $this->actingAs($student)->get(route('modules.show', $module))->assertForbidden();
        $this->actingAs($student)->get(route('modules.download', $module))->assertForbidden();
    }

    public function test_teacher_cannot_change_another_teachers_records(): void
    {
        $other = User::factory()->teacher()->create();

        $grade = Grade::factory()->create();
        $quiz = Quiz::factory()->create();
        $module = Module::factory()->create();

        $this->actingAs($other)->delete(route('grades.destroy', $grade))->assertForbidden();
        $this->actingAs($other)->delete(route('quizzes.destroy', $quiz))->assertForbidden();
        $this->actingAs($other)->delete(route('modules.destroy', $module))->assertForbidden();
    }

    public function test_grade_update_cannot_duplicate_another_quarter_row(): void
    {
        $teacher = User::factory()->teacher()->create();
        $student = User::factory()->create();
        Grade::factory()->create(['teacher_id' => $teacher->id, 'student_id' => $student->id, 'subject' => 'Math', 'quarter' => 1]);
        $second = Grade::factory()->create(['teacher_id' => $teacher->id, 'student_id' => $student->id, 'subject' => 'Math', 'quarter' => 2]);

        $this->actingAs($teacher)->patch(route('grades.update', $second), [
            'student_id' => $student->id, 'subject' => 'Math', 'quarter' => 1, 'grade' => 90,
        ])->assertSessionHasErrors('quarter');
    }
}
