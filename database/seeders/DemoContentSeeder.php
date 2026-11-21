<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\ForumReply;
use App\Models\ForumThread;
use App\Models\Grade;
use App\Models\GroupMessage;
use App\Models\Module;
use App\Models\Opportunity;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\Schedule;
use App\Models\StudyGroup;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

// Demo content so every page has something to show. Run after UserSeeder (it needs the six accounts).
class DemoContentSeeder extends Seeder
{
    public function run(): void
    {
        $teacher = User::where('email', 'teacher@educonnect.test')->firstOrFail();
        $student = User::where('email', 'student@educonnect.test')->firstOrFail();
        $student2 = User::where('email', 'student2@educonnect.test')->firstOrFail();
        $org = User::where('email', 'org@educonnect.test')->firstOrFail();

        $modules = $this->modules($teacher);
        $this->quiz($teacher, $student, $modules['Mathematics']);
        $this->announcements($teacher);
        $this->grades($teacher, $student, $student2);
        $this->schedules($teacher);
        $this->forum($student, $student2, $teacher);
        $this->studyGroup($student, $student2);
        $this->opportunities($org);
    }

    // 3 modules (Mathematics, Science, English) and 1 study guide, all Grade 10, sharing one sample PDF.
    private function modules(User $teacher): array
    {
        Storage::disk('public')->put('modules/sample.pdf', $this->samplePdf());

        $rows = [
            ['Mathematics', 'module', 'Algebra Basics: Solving Linear Equations', 'algebra-basics.pdf',
                'Step-by-step lessons on solving one-variable linear equations, with worked examples.'],
            ['Science', 'module', 'Cells and Living Systems', 'cells-and-living-systems.pdf',
                'The parts of a cell, how cells work together and how living systems stay in balance.'],
            ['English', 'module', 'Reading Comprehension and Literary Devices', 'reading-comprehension.pdf',
                'Finding the main idea, making inferences and spotting metaphors and similes.'],
            ['Mathematics', 'study_guide', 'Study Guide: Quadratic Equations', 'quadratics-study-guide.pdf',
                'A short review sheet on factoring and the quadratic formula, made for exam week.'],
        ];

        $modules = [];
        foreach ($rows as [$subject, $type, $title, $fileName, $description]) {
            $module = Module::create([
                'teacher_id' => $teacher->id,
                'title' => $title,
                'subject' => $subject,
                'grade_level' => 'Grade 10',
                'type' => $type,
                'description' => $description,
                'file_path' => 'modules/sample.pdf',
                'original_name' => $fileName,
            ]);
            $modules[$subject] ??= $module; // the first module of each subject
        }

        return $modules;
    }

    // 1 published 5-question Mathematics quiz, and 1 attempt by student@.
    private function quiz(User $teacher, User $student, Module $module): void
    {
        $quiz = Quiz::create([
            'teacher_id' => $teacher->id,
            'module_id' => $module->id,
            'title' => 'Linear Equations Practice Quiz',
            'subject' => 'Mathematics',
            'grade_level' => 'Grade 10',
            'description' => 'Five questions to check that you can solve and read linear equations.',
            'is_published' => true,
        ]);

        // [question, option a, b, c, d, correct option]
        $rows = [
            ['Solve for x: 2x + 6 = 14', '2', '4', '6', '8', 'b'],
            ['What is the slope of the line y = 3x - 5?', '3', '-5', '5', '1/3', 'a'],
            ['If 3(x - 2) = 12, what is x?', '4', '6', '8', '10', 'b'],
            ['Which point lies on the line y = 2x + 1?', '(1, 2)', '(2, 5)', '(3, 6)', '(0, 0)', 'b'],
            ['Simplify: 5x + 3x - 2x', '6x', '8x', '10x', '4x', 'a'],
        ];

        foreach ($rows as [$text, $a, $b, $c, $d, $correct]) {
            $quiz->questions()->create([
                'question' => $text,
                'option_a' => $a,
                'option_b' => $b,
                'option_c' => $c,
                'option_d' => $d,
                'correct_option' => $correct,
            ]);
        }

        // The student's answers: right on every question except number 4.
        $chosen = ['b', 'a', 'b', 'c', 'a'];
        $answers = [];
        $score = 0;
        foreach ($quiz->questions as $index => $question) {
            $answers[$question->id] = $chosen[$index];
            if ($chosen[$index] === $question->correct_option) {
                $score++;
            }
        }

        QuizAttempt::create([
            'quiz_id' => $quiz->id,
            'user_id' => $student->id,
            'score' => $score,
            'total' => $quiz->questions->count(),
            'answers' => $answers,
        ]);
    }

    // 2 announcements: one for everyone, one for parents.
    private function announcements(User $teacher): void
    {
        Announcement::create([
            'user_id' => $teacher->id,
            'title' => 'Welcome to the new school term',
            'body' => "Classes start on Monday at 8:00 AM.\nPlease check the class schedule and bring your learning materials.",
            'audience' => 'all',
        ]);

        Announcement::create([
            'user_id' => $teacher->id,
            'title' => 'Parent-teacher conference this Friday',
            'body' => "Parents are invited to meet the Grade 10 teachers this Friday at 3:00 PM in the school library.\nPlease bring your child's report card.",
            'audience' => 'parents',
        ]);
    }

    // Q1 and Q2 grades in 4 subjects for student@ and student2@.
    private function grades(User $teacher, User $student, User $student2): void
    {
        // subject => [student Q1, student Q2, student2 Q1, student2 Q2]
        $rows = [
            'Mathematics' => [88, 91, 79, 83],
            'Science' => [85, 87, 82, 84],
            'English' => [90, 92, 88, 90],
            'Filipino' => [86, 89, 84, 85],
        ];

        foreach ($rows as $subject => [$s1q1, $s1q2, $s2q1, $s2q2]) {
            foreach ([[$student, 1, $s1q1], [$student, 2, $s1q2], [$student2, 1, $s2q1], [$student2, 2, $s2q2]] as [$who, $quarter, $grade]) {
                Grade::create([
                    'student_id' => $who->id,
                    'teacher_id' => $teacher->id,
                    'subject' => $subject,
                    'quarter' => $quarter,
                    'grade' => $grade,
                    'remarks' => ($subject === 'Mathematics' && $quarter === 2 && $who->id === $student->id) ? 'Great improvement' : null,
                ]);
            }
        }
    }

    // Grade 10 schedule, Monday to Friday: a morning and a late-morning class every day.
    private function schedules(User $teacher): void
    {
        // day => [[title, subject, start, end, room], ...]
        $week = [
            'Monday' => [['Algebra', 'Mathematics', '08:00', '09:00', 'Room 101'], ['Biology', 'Science', '10:00', '11:00', 'Lab 1']],
            'Tuesday' => [['Reading and Writing', 'English', '08:00', '09:00', 'Room 102'], ['Wika at Panitikan', 'Filipino', '10:00', '11:00', 'Room 103']],
            'Wednesday' => [['Algebra', 'Mathematics', '08:00', '09:00', 'Room 101'], ['Physical Science', 'Science', '10:00', '11:00', 'Lab 1']],
            'Thursday' => [['Literature', 'English', '08:00', '09:00', 'Room 102'], ['Wika at Panitikan', 'Filipino', '10:00', '11:00', 'Room 103']],
            'Friday' => [['Mathematics Review', 'Mathematics', '08:00', '09:00', 'Room 101'], ['Science Laboratory', 'Science', '10:00', '11:00', 'Lab 1']],
        ];

        foreach ($week as $day => $classes) {
            foreach ($classes as [$title, $subject, $start, $end, $room]) {
                Schedule::create([
                    'user_id' => $teacher->id,
                    'title' => $title,
                    'subject' => $subject,
                    'grade_level' => 'Grade 10',
                    'day' => $day,
                    'start_time' => $start,
                    'end_time' => $end,
                    'room' => $room,
                ]);
            }
        }
    }

    // 1 forum thread with 2 replies.
    private function forum(User $student, User $student2, User $teacher): void
    {
        $thread = ForumThread::create([
            'user_id' => $student->id,
            'title' => 'How do I solve quadratic equations by factoring?',
            'body' => "I understand linear equations, but I get stuck on x² + 5x + 6 = 0.\nWhat is the first step?",
        ]);

        ForumReply::create([
            'forum_thread_id' => $thread->id,
            'user_id' => $student2->id,
            'body' => 'Look for two numbers that multiply to 6 and add up to 5. That gives you 2 and 3.',
        ]);

        ForumReply::create([
            'forum_thread_id' => $thread->id,
            'user_id' => $teacher->id,
            'body' => 'Good tip! Once you have (x + 2)(x + 3) = 0, set each bracket to zero. Post your answer and we will check it together.',
        ]);
    }

    // 1 study group with both students and 3 messages.
    private function studyGroup(User $student, User $student2): void
    {
        $group = StudyGroup::create([
            'owner_id' => $student->id,
            'name' => 'Grade 10 Math Study Circle',
            'subject' => 'Mathematics',
            'description' => 'We practise problems together before every quiz.',
        ]);

        $group->members()->attach([$student->id, $student2->id]);

        foreach ([
            [$student, 'Welcome! Let us meet online on Saturday to review linear equations.'],
            [$student2, 'Saturday works for me. Can we also go over the practice quiz?'],
            [$student, 'Yes, I will bring my notes from Algebra Basics.'],
        ] as [$author, $body]) {
            GroupMessage::create(['study_group_id' => $group->id, 'user_id' => $author->id, 'body' => $body]);
        }
    }

    // 3 postings by org@: a scholarship and a tutoring program (approved) and an event (pending).
    private function opportunities(User $org): void
    {
        Opportunity::create([
            'user_id' => $org->id,
            'type' => 'scholarship',
            'title' => 'Barangay Excellence Scholarship',
            'description' => 'A tuition grant for Grade 10 to Grade 12 students with a general average of 85 or higher. Apply with your latest report card.',
            'location' => 'Municipal Hall',
            'deadline' => now()->addDays(30)->toDateString(),
            'link' => 'https://example.org/scholarship',
            'status' => 'approved',
        ]);

        Opportunity::create([
            'user_id' => $org->id,
            'type' => 'tutoring',
            'title' => 'Free Weekend Math Tutoring',
            'description' => 'Volunteer tutors help students with algebra and geometry every Saturday morning. No registration fee.',
            'location' => 'Community Library',
            'event_date' => now()->addDays(10)->toDateString(),
            'link' => 'https://example.org/tutoring',
            'status' => 'approved',
        ]);

        Opportunity::create([
            'user_id' => $org->id,
            'type' => 'event',
            'title' => 'Science Fair Volunteer Day',
            'description' => 'Help set up the town science fair and meet local scientists. Waiting for the school admin to approve this posting.',
            'location' => 'Town Gymnasium',
            'event_date' => now()->addDays(45)->toDateString(),
            'status' => 'pending',
        ]);
    }

    // A tiny valid one-page PDF built with plain PHP (no package): header, five objects, cross-reference table, trailer.
    private function samplePdf(): string
    {
        $stream = "BT /F1 24 Tf 72 700 Td (EduConnect sample module) Tj ET\n"
            ."BT /F1 12 Tf 72 670 Td (This is a demo file for the seeded modules.) Tj ET";

        $objects = [
            '<< /Type /Catalog /Pages 2 0 R >>',
            '<< /Type /Pages /Kids [3 0 R] /Count 1 >>',
            '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Contents 4 0 R /Resources << /Font << /F1 5 0 R >> >> >>',
            '<< /Length '.strlen($stream)." >>\nstream\n".$stream."\nendstream",
            '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>',
        ];

        $pdf = "%PDF-1.4\n";
        $offsets = [];
        foreach ($objects as $index => $body) {
            $offsets[] = strlen($pdf); // where this object starts, for the cross-reference table
            $pdf .= ($index + 1)." 0 obj\n".$body."\nendobj\n";
        }

        $xrefStart = strlen($pdf);
        $pdf .= "xref\n0 ".(count($objects) + 1)."\n0000000000 65535 f \n";
        foreach ($offsets as $offset) {
            $pdf .= sprintf("%010d 00000 n \n", $offset);
        }
        $pdf .= 'trailer'."\n".'<< /Size '.(count($objects) + 1).' /Root 1 0 R >>'."\nstartxref\n".$xrefStart."\n%%EOF\n";

        return $pdf;
    }
}
