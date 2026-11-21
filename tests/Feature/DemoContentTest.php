<?php

namespace Tests\Feature;

use App\Models\Announcement;
use App\Models\ForumReply;
use App\Models\ForumThread;
use App\Models\Grade;
use App\Models\Module;
use App\Models\Opportunity;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\Schedule;
use App\Models\StudyGroup;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DemoContentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // The seeder writes a PDF; keep it off the real storage folder while testing.
        Storage::fake('public');
        $this->seed(DatabaseSeeder::class);
    }

    private function account(string $name): User
    {
        return User::where('email', $name.'@educonnect.test')->firstOrFail();
    }

    public function test_demo_content_matches_the_plan(): void
    {
        // 3 modules + 1 study guide, all Grade 10, by teacher@.
        $this->assertSame(3, Module::where('type', 'module')->count());
        $this->assertSame(1, Module::where('type', 'study_guide')->count());
        $this->assertEqualsCanonicalizing(['English', 'Mathematics', 'Science'], Module::where('type', 'module')->pluck('subject')->all());
        $this->assertSame(0, Module::where('grade_level', '!=', 'Grade 10')->count());
        $this->assertSame(0, Module::where('teacher_id', '!=', $this->account('teacher')->id)->count());

        // 1 published 5-question Mathematics quiz with 1 attempt by student@ (4 of 5).
        $quiz = Quiz::firstOrFail();
        $this->assertSame(1, Quiz::count());
        $this->assertTrue($quiz->is_published);
        $this->assertSame('Mathematics', $quiz->subject);
        $this->assertSame(5, Question::where('quiz_id', $quiz->id)->count());
        $attempt = QuizAttempt::firstOrFail();
        $this->assertSame($this->account('student')->id, $attempt->user_id);
        $this->assertSame([4, 5], [$attempt->score, $attempt->total]);

        // 2 announcements (all, parents).
        $this->assertEqualsCanonicalizing(['all', 'parents'], Announcement::pluck('audience')->all());

        // Q1 and Q2 grades for both students in 4 subjects.
        $this->assertSame(16, Grade::count());
        $this->assertSame(8, Grade::where('student_id', $this->account('student')->id)->count());
        $this->assertSame(8, Grade::where('student_id', $this->account('student2')->id)->count());
        $this->assertSame(4, Grade::distinct('subject')->count('subject'));

        // Grade 10 schedule, Monday to Friday.
        $this->assertEqualsCanonicalizing(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'], Schedule::distinct()->pluck('day')->all());
        $this->assertSame(0, Schedule::where('grade_level', '!=', 'Grade 10')->count());

        // 1 forum thread with 2 replies.
        $this->assertSame(1, ForumThread::count());
        $this->assertSame(2, ForumReply::count());

        // 1 study group with both students and 3 messages.
        $group = StudyGroup::firstOrFail();
        $this->assertSame(1, StudyGroup::count());
        $this->assertTrue($group->hasMember($this->account('student')));
        $this->assertTrue($group->hasMember($this->account('student2')));
        $this->assertSame(3, $group->messages()->count());

        // 3 postings by org@: scholarship and tutoring approved, event pending.
        $this->assertSame(3, Opportunity::where('user_id', $this->account('org')->id)->count());
        $this->assertSame('approved', Opportunity::where('type', 'scholarship')->value('status'));
        $this->assertSame('approved', Opportunity::where('type', 'tutoring')->value('status'));
        $this->assertSame('pending', Opportunity::where('type', 'event')->value('status'));
    }

    public function test_sample_pdf_is_a_valid_pdf(): void
    {
        $pdf = Storage::disk('public')->get('modules/sample.pdf');

        $this->assertStringStartsWith('%PDF-1.4', $pdf);
        $this->assertStringEndsWith("%%EOF\n", $pdf);

        // startxref points at the xref table, and every xref entry points at its own "N 0 obj".
        preg_match('/startxref\n(\d+)\n%%EOF/', $pdf, $start);
        $this->assertSame('xref', substr($pdf, (int) $start[1], 4));

        preg_match_all('/(\d{10}) 00000 n /', $pdf, $entries);
        $this->assertCount(5, $entries[1]);
        foreach ($entries[1] as $index => $offset) {
            $this->assertStringStartsWith(($index + 1).' 0 obj', substr($pdf, (int) $offset, 12));
        }
    }

    public function test_the_six_seeded_accounts_can_log_in(): void
    {
        foreach (['admin', 'teacher', 'student', 'student2', 'parent', 'org'] as $name) {
            $this->post('/logout');
            $this->post('/login', ['email' => $name.'@educonnect.test', 'password' => 'password']);

            $this->assertAuthenticatedAs($this->account($name));
        }
    }

    public function test_every_role_can_open_its_pages_with_the_demo_data(): void
    {
        $student = $this->account('student');
        $module = Module::firstOrFail();
        $quiz = Quiz::firstOrFail();
        $attempt = QuizAttempt::firstOrFail();
        $thread = ForumThread::firstOrFail();
        $group = StudyGroup::firstOrFail();
        $approved = Opportunity::where('type', 'scholarship')->firstOrFail();
        $pending = Opportunity::where('type', 'event')->firstOrFail();

        // student@: has already taken the quiz, so opening it shows the result.
        $this->actingAs($student);
        foreach ([
            route('dashboard'), route('modules.index'), route('modules.show', $module), route('modules.download', $module),
            route('quizzes.index'), route('attempts.index'), route('attempts.show', $attempt), route('grades.index'),
            route('grades.report', $student), route('schedules.index'), route('announcements.index'), route('threads.index'),
            route('threads.show', $thread), route('study-groups.index'), route('study-groups.show', $group),
            route('opportunities.index'), route('opportunities.show', $approved), route('profile.edit'),
        ] as $url) {
            $this->get($url)->assertOk();
        }
        $this->get(route('quizzes.show', $quiz))->assertRedirect(route('attempts.show', $attempt));
        $this->get(route('dashboard'))->assertSee('My quiz progress')->assertSee('Mathematics')->assertSee('4 / 5');

        // student2@ has not taken the quiz, so they see its questions.
        $this->actingAs($this->account('student2'))->get(route('quizzes.show', $quiz))->assertOk()->assertSee('Submit answers');

        // parent@ sees only their child (student@).
        $this->actingAs($this->account('parent'));
        foreach ([route('dashboard'), route('grades.index'), route('grades.report', $student), route('schedules.index'), route('attempts.show', $attempt), route('announcements.index')] as $url) {
            $this->get($url)->assertOk();
        }
        $this->get(route('grades.index'))->assertSee('Student One')->assertDontSee('Student Two');
        $this->get(route('grades.report', $this->account('student2')))->assertForbidden();
        $this->get(route('announcements.index'))->assertSee('Parent-teacher conference')->assertSee('Welcome to the new school term');

        // teacher@
        $this->actingAs($this->account('teacher'));
        foreach ([
            route('dashboard'), route('modules.index'), route('modules.create'), route('modules.edit', $module),
            route('quizzes.index'), route('quizzes.show', $quiz), route('quizzes.create'), route('quizzes.edit', $quiz),
            route('grades.index'), route('grades.create'), route('schedules.index'), route('schedules.create'),
            route('announcements.index'), route('announcements.create'), route('threads.index'), route('study-groups.index'),
            route('attempts.show', $attempt), route('grades.report', $student),
        ] as $url) {
            $this->get($url)->assertOk();
        }

        // org@: its own postings, including the pending one.
        $this->actingAs($this->account('org'));
        foreach ([route('dashboard'), route('opportunities.mine'), route('opportunities.create'), route('opportunities.edit', $pending), route('opportunities.show', $pending)] as $url) {
            $this->get($url)->assertOk();
        }
        $this->get(route('opportunities.mine'))->assertSee('Pending')->assertSee('Approved');

        // admin@: sees everything, including the posting waiting for approval and any study group.
        $this->actingAs($this->account('admin'));
        foreach ([route('dashboard'), route('admin.users.index'), route('admin.users.create'), route('admin.links.index'), route('admin.opportunities.index'), route('study-groups.show', $group), route('opportunities.show', $pending)] as $url) {
            $this->get($url)->assertOk();
        }
        $this->get(route('admin.opportunities.index'))->assertSee('Science Fair Volunteer Day');
        $this->get(route('admin.links.index'))->assertSee('Parent User');
    }

    public function test_the_public_sees_only_approved_postings(): void
    {
        $this->get(route('opportunities.index'))
            ->assertOk()
            ->assertSee('Barangay Excellence Scholarship')
            ->assertSee('Free Weekend Math Tutoring')
            ->assertDontSee('Science Fair Volunteer Day');

        $this->get(route('opportunities.show', Opportunity::where('type', 'event')->firstOrFail()))->assertNotFound();
    }
}
