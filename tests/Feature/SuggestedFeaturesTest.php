<?php

namespace Tests\Feature;

use App\Models\ForumThread;
use App\Models\Module;
use App\Models\Opportunity;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuggestedFeaturesTest extends TestCase
{
    use RefreshDatabase;

    // Feature 1: keyword search on modules, forum threads and opportunities.
    public function test_keyword_search_filters_modules_threads_and_opportunities(): void
    {
        $student = User::factory()->create(['grade_level' => 'Grade 10']);

        Module::factory()->create(['title' => 'Fractions Workbook', 'grade_level' => 'Grade 10', 'subject' => 'Math']);
        Module::factory()->create(['title' => 'Cell Biology Notes', 'grade_level' => 'Grade 10', 'subject' => 'Science']);
        ForumThread::create(['title' => 'Help with fractions', 'body' => 'Please explain.', 'user_id' => $student->id]);
        ForumThread::create(['title' => 'Chemistry homework', 'body' => 'Stuck on question 3.', 'user_id' => $student->id]);
        Opportunity::factory()->approved()->create(['title' => 'Science Scholarship', 'type' => 'scholarship']);
        Opportunity::factory()->approved()->create(['title' => 'Football Clinic', 'type' => 'tutoring']);

        $this->actingAs($student);

        // Modules: matches the title only, and combines with the existing subject filter.
        $this->get(route('modules.index', ['q' => 'fraction']))
            ->assertSee('Fractions Workbook')->assertDontSee('Cell Biology Notes');
        $this->get(route('modules.index', ['q' => 'fraction', 'subject' => 'Science']))
            ->assertDontSee('Fractions Workbook')->assertDontSee('Cell Biology Notes');

        // Forum threads.
        $this->get(route('threads.index', ['q' => 'fraction']))
            ->assertSee('Help with fractions')->assertDontSee('Chemistry homework');

        // Opportunities are public, so search them as a guest. It combines with the type filter.
        auth()->logout();
        $this->get(route('opportunities.index', ['q' => 'scholar']))
            ->assertSee('Science Scholarship')->assertDontSee('Football Clinic');
        $this->get(route('opportunities.index', ['q' => 'scholar', 'type' => 'event']))
            ->assertDontSee('Science Scholarship');

        // An empty search box shows everything.
        $this->get(route('opportunities.index', ['q' => '']))
            ->assertSee('Science Scholarship')->assertSee('Football Clinic');
    }

    // Feature 2: student progress card on the student and parent dashboards.
    public function test_progress_card_shows_attempts_average_and_best_score(): void
    {
        $student = User::factory()->create();
        $parent = User::factory()->parent()->create();
        $parent->children()->attach($student->id);

        // A quiz can be taken once, so each quiz has one attempt: Alpha 1/2 (50%), Beta 2/2 (100%), Gamma 1/4 (25%).
        $alpha = Quiz::factory()->published()->create(['title' => 'Quiz Alpha']);
        $beta = Quiz::factory()->published()->create(['title' => 'Quiz Beta']);
        $gamma = Quiz::factory()->published()->create(['title' => 'Quiz Gamma']);

        foreach ([[$alpha, 1, 2], [$beta, 2, 2], [$gamma, 1, 4]] as [$quiz, $score, $total]) {
            QuizAttempt::create(['quiz_id' => $quiz->id, 'user_id' => $student->id, 'score' => $score, 'total' => $total, 'answers' => []]);
        }

        // Overall: 3 attempts, (50 + 100 + 25) / 3 = 58%. Beta: best score 2 / 2.
        $this->actingAs($student)->get(route('dashboard'))
            ->assertOk()
            ->assertSee('My quiz progress')
            ->assertSeeText('3 attempts')
            ->assertSee('58%')
            ->assertSee('Quiz Beta')
            ->assertSee('2 / 2 (100%)');

        // The linked parent sees the same numbers for the child.
        $this->actingAs($parent)->get(route('dashboard'))
            ->assertOk()
            ->assertSeeText('3 attempts')
            ->assertSee('58%')
            ->assertSee('Quiz Alpha');

        // A parent with no linked child sees nothing of this student's progress.
        $this->actingAs(User::factory()->parent()->create())->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee('Quiz Alpha');

        // A student who has taken nothing sees the empty message, not a table.
        $this->actingAs(User::factory()->create())->get(route('dashboard'))
            ->assertOk()
            ->assertSee('No quizzes taken yet.');
    }
}
