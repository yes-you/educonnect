<?php

namespace Tests\Feature;

use App\Models\Announcement;
use App\Models\ForumThread;
use App\Models\Grade;
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

    // Feature 4: "new" announcements badge in the navbar.
    public function test_navbar_badge_counts_new_announcements_and_resets_when_the_page_is_opened(): void
    {
        $teacher = User::factory()->teacher()->create();
        $student = User::factory()->create();
        $parent = User::factory()->parent()->create();

        Announcement::factory()->create(['user_id' => $teacher->id, 'audience' => 'all']);
        Announcement::factory()->create(['user_id' => $teacher->id, 'audience' => 'parents']);
        Announcement::factory()->create(['user_id' => $teacher->id, 'audience' => 'students']);

        // Each role counts only the announcements meant for it.
        $this->assertSame(2, $student->newAnnouncementsCount()); // all + students
        $this->assertSame(2, $parent->newAnnouncementsCount());  // all + parents
        $this->assertSame(0, $teacher->newAnnouncementsCount()); // the author's own posts do not count

        // The badge shows in the navbar with a hidden label for screen readers.
        $this->actingAs($student)->get(route('dashboard'))
            ->assertOk()
            ->assertSee('2<span class="visually-hidden"> new announcements</span>', false);

        // Opening the Announcements page marks them seen: the count and the badge go away.
        $this->get(route('announcements.index'))->assertOk()->assertDontSee('new announcement');
        $this->assertSame(0, $student->fresh()->newAnnouncementsCount());
        $this->get(route('dashboard'))->assertDontSee('new announcement');

        // An announcement posted after that shows up as 1 new (singular label).
        $this->travel(5)->seconds();
        Announcement::factory()->create(['user_id' => $teacher->id, 'audience' => 'students']);
        $this->assertSame(1, $student->fresh()->newAnnouncementsCount());
        $this->get(route('dashboard'))->assertSee('1<span class="visually-hidden"> new announcement</span>', false);

        // Another student who never opened the page still sees all 3 that are for students.
        $this->assertSame(3, User::factory()->create()->newAnnouncementsCount());
    }

    // Feature 3: printable report card.
    public function test_report_card_is_visible_only_to_the_right_people(): void
    {
        $student = User::factory()->create(['name' => 'Report Student', 'grade_level' => 'Grade 10']);
        $teacher = User::factory()->teacher()->create();

        // Math: Q1 90 and Q2 80 (final 85). Science: Q1 70 (final 70). General average (85 + 70) / 2 = 77.50.
        foreach ([['Math', 1, 90], ['Math', 2, 80], ['Science', 1, 70]] as [$subject, $quarter, $grade]) {
            Grade::factory()->create(['student_id' => $student->id, 'teacher_id' => $teacher->id, 'subject' => $subject, 'quarter' => $quarter, 'grade' => $grade]);
        }

        // The student sees their own report with the right numbers and a Print button.
        $this->actingAs($student)->get(route('grades.report', $student))
            ->assertOk()
            ->assertSee('Report Student')
            ->assertSee('Grade 10')
            ->assertSee('85.00')
            ->assertSee('77.50')
            ->assertSee('window.print()', false);

        // A linked parent, a teacher and an admin can open it.
        $parent = User::factory()->parent()->create();
        $parent->children()->attach($student->id);
        $this->actingAs($parent)->get(route('grades.report', $student))->assertOk();
        $this->actingAs($teacher)->get(route('grades.report', $student))->assertOk();
        $this->actingAs(User::factory()->admin()->create())->get(route('grades.report', $student))->assertOk();

        // An unrelated parent, another student and an organization may not.
        $this->actingAs(User::factory()->parent()->create())->get(route('grades.report', $student))->assertForbidden();
        $this->actingAs(User::factory()->create())->get(route('grades.report', $student))->assertForbidden();
        $this->actingAs(User::factory()->organization()->create())->get(route('grades.report', $student))->assertForbidden();

        // A guest is sent to log in, and only students have a report card.
        auth()->logout();
        $this->get(route('grades.report', $student))->assertRedirect(route('login'));
        $this->actingAs($teacher)->get(route('grades.report', $teacher))->assertNotFound();

        // A student with no grades gets a clear message, not an empty table.
        $newStudent = User::factory()->create();
        $this->actingAs($newStudent)->get(route('grades.report', $newStudent))
            ->assertOk()
            ->assertSee('No grades have been encoded');
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
