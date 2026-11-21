<?php

namespace Tests\Feature;

use App\Models\Module;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuizScoringTest extends TestCase
{
    use RefreshDatabase;

    // A published quiz with 3 questions whose correct answer is always "a".
    private function quizWithThreeQuestions(bool $published = true): Quiz
    {
        $quiz = Quiz::factory()->create(['is_published' => $published, 'grade_level' => 'Grade 10']);
        Question::factory()->count(3)->create(['quiz_id' => $quiz->id, 'correct_option' => 'a']);

        return $quiz->load('questions');
    }

    public function test_student_answers_two_of_three_correctly(): void
    {
        $quiz = $this->quizWithThreeQuestions();
        $student = User::factory()->create();
        [$q1, $q2, $q3] = $quiz->questions;

        $this->actingAs($student)->post(route('attempts.store', $quiz), [
            'answers' => [$q1->id => 'a', $q2->id => 'a', $q3->id => 'b'],
        ])->assertRedirect();

        $this->assertDatabaseHas('quiz_attempts', [
            'quiz_id' => $quiz->id,
            'user_id' => $student->id,
            'score' => 2,
            'total' => 3,
        ]);
    }

    public function test_a_quiz_can_only_be_attempted_once(): void
    {
        $quiz = $this->quizWithThreeQuestions();
        $student = User::factory()->create();
        [$q1, $q2, $q3] = $quiz->questions;

        $this->actingAs($student)->post(route('attempts.store', $quiz), [
            'answers' => [$q1->id => 'a', $q2->id => 'a', $q3->id => 'b'],
        ]);
        $first = $student->quizAttempts()->first();

        // A second submit, even with better answers, keeps the first result and saves nothing new.
        $this->post(route('attempts.store', $quiz), [
            'answers' => [$q1->id => 'a', $q2->id => 'a', $q3->id => 'a'],
        ])->assertRedirect(route('attempts.show', $first))->assertSessionHas('error');

        $this->assertDatabaseCount('quiz_attempts', 1);
        $this->assertSame(2, $first->fresh()->score);

        // Opening the quiz again shows the result, not the questions.
        $this->get(route('quizzes.show', $quiz))->assertRedirect(route('attempts.show', $first));

        // The quiz list shows the result instead of "Not taken yet".
        $this->get(route('quizzes.index'))->assertOk()->assertSee('2 / 3 (67%)')->assertDontSee('Not taken yet');

        // Another student can still take the same quiz.
        $this->actingAs(User::factory()->create())
            ->get(route('quizzes.show', $quiz))
            ->assertOk()
            ->assertSee('Submit answers');
    }

    public function test_database_itself_rejects_a_second_attempt(): void
    {
        $quiz = $this->quizWithThreeQuestions();
        $student = User::factory()->create();
        $row = ['quiz_id' => $quiz->id, 'user_id' => $student->id, 'score' => 1, 'total' => 3, 'answers' => []];

        QuizAttempt::create($row);

        // Covers two submits at the same moment, where the controller check cannot help.
        $this->expectException(UniqueConstraintViolationException::class);
        QuizAttempt::create($row);
    }

    public function test_missing_answer_is_rejected(): void
    {
        $quiz = $this->quizWithThreeQuestions();
        [$q1, $q2] = $quiz->questions;

        $this->actingAs(User::factory()->create())
            ->post(route('attempts.store', $quiz), ['answers' => [$q1->id => 'a', $q2->id => 'a']])
            ->assertSessionHasErrors('answers.'.$quiz->questions[2]->id);

        $this->assertDatabaseCount('quiz_attempts', 0);
    }

    public function test_student_cannot_open_unpublished_quiz(): void
    {
        $quiz = $this->quizWithThreeQuestions(published: false);

        $this->actingAs(User::factory()->create())
            ->get(route('quizzes.show', $quiz))
            ->assertNotFound();
    }

    public function test_student_cannot_open_module_create_page(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('modules.create'))
            ->assertForbidden();
    }

    public function test_student_cannot_reach_other_grade_levels(): void
    {
        $grade9Student = User::factory()->create(['grade_level' => 'Grade 9']);
        $quiz = $this->quizWithThreeQuestions(); // Grade 10
        $module = Module::factory()->create(['grade_level' => 'Grade 10', 'title' => 'Grade Ten Module']);
        $q = $quiz->questions;

        $this->actingAs($grade9Student);

        // Lists show nothing from Grade 10, even when the filter is changed by hand in the URL.
        $this->get(route('modules.index', ['grade_level' => 'Grade 10']))->assertOk()->assertDontSee('Grade Ten Module');
        $this->get(route('quizzes.index', ['grade_level' => 'Grade 10']))->assertOk()->assertDontSee($quiz->title);

        // Direct links are blocked.
        $this->get(route('modules.show', $module))->assertForbidden();
        $this->get(route('modules.download', $module))->assertForbidden();
        $this->get(route('quizzes.show', $quiz))->assertNotFound();
        $this->post(route('attempts.store', $quiz), [
            'answers' => [$q[0]->id => 'a', $q[1]->id => 'a', $q[2]->id => 'a'],
        ])->assertNotFound();
        $this->assertDatabaseCount('quiz_attempts', 0);
    }

    public function test_quiz_page_does_not_leak_correct_answers(): void
    {
        $quiz = $this->quizWithThreeQuestions();

        $this->actingAs(User::factory()->create())
            ->get(route('quizzes.show', $quiz))
            ->assertOk()
            ->assertDontSee('correct_option');
    }
}
