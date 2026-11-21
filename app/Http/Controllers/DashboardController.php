<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\ForumThread;
use App\Models\Grade;
use App\Models\Module;
use App\Models\Opportunity;
use App\Models\Quiz;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        return view('dashboard', [
            // Every role: the latest 3 announcements meant for it.
            'announcements' => Announcement::with('author')->visibleTo($user)->latest()->take(3)->get(),
            // Role widgets: dashboard/_admin, _teacher, _student, _parent or _organization.
            'data' => $this->dataFor($user),
        ]);
    }

    private function dataFor(User $user): array
    {
        return match ($user->role) {
            'student' => $this->studentData($user),
            'parent' => $this->parentData($user),
            'teacher' => $this->teacherData($user),
            'organization' => $this->organizationData($user),
            default => $this->adminData(),
        };
    }

    private function studentData(User $user): array
    {
        return [
            'modules' => Module::where('grade_level', $user->grade_level)->latest()->take(5)->get(),
            // Published quizzes of the student's grade that they have not taken yet.
            'quizzes' => Quiz::where('is_published', true)
                ->where('grade_level', $user->grade_level)
                ->whereDoesntHave('attempts', fn ($query) => $query->where('user_id', $user->id))
                ->withCount('questions')
                ->latest()
                ->take(5)
                ->get(),
            'attempts' => $user->quizAttempts()->with('quiz')->latest()->take(5)->get(),
        ];
    }

    private function parentData(User $user): array
    {
        // Only children linked to this parent in parent_student.
        $children = $user->children()->orderBy('name')->get();

        foreach ($children as $child) {
            $child->setRelation('latestGrades', $child->grades()->latest()->take(5)->get());
            $child->setRelation('latestAttempts', $child->quizAttempts()->with('quiz')->latest()->take(3)->get());
        }

        return ['children' => $children];
    }

    private function teacherData(User $user): array
    {
        return [
            'moduleCount' => $user->modules()->count(),
            'quizCount' => $user->quizzes()->count(),
            'gradeCount' => Grade::where('teacher_id', $user->id)->count(),
        ];
    }

    private function organizationData(User $user): array
    {
        return ['opportunities' => $user->opportunities()->latest()->take(10)->get()];
    }

    private function adminData(): array
    {
        return [
            'roleCounts' => User::selectRaw('role, count(*) as total')->groupBy('role')->pluck('total', 'role'),
            'moduleCount' => Module::count(),
            'quizCount' => Quiz::count(),
            'threadCount' => ForumThread::count(),
            'pendingCount' => Opportunity::where('status', 'pending')->count(),
        ];
    }
}
