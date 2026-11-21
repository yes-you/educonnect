<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class GradeController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        // A student sees only their own grades.
        if ($user->hasRole('student')) {
            return view('grades.index', ['grades' => $user->grades]);
        }

        // A parent sees only the children linked to them in parent_student.
        if ($user->hasRole('parent')) {
            return view('grades.index', [
                'children' => $user->children()->with('grades')->orderBy('name')->get(),
            ]);
        }

        // Teacher: grades they encoded. Admin: all grades.
        $grades = Grade::with(['student', 'teacher'])
            ->when($user->hasRole('teacher'), fn ($query) => $query->where('teacher_id', $user->id))
            ->when($request->filled('student_id'), fn ($query) => $query->where('student_id', $request->student_id))
            ->when($request->filled('subject'), fn ($query) => $query->where('subject', 'like', '%'.$request->subject.'%'))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('grades.index', [
            'grades' => $grades,
            'students' => $this->students(),
        ]);
    }

    // Printable report card for one student.
    public function report(User $student): View
    {
        abort_unless($student->role === 'student', 404);

        Gate::authorize('viewReport', [Grade::class, $student]);

        // One row per subject: the grade of each quarter and the final grade (average of its quarters).
        $subjects = $student->grades()->orderBy('subject')->get()
            ->groupBy('subject')
            ->map(fn ($rows) => [
                'quarters' => $rows->pluck('grade', 'quarter'),
                'final' => round($rows->avg('grade'), 2),
            ]);

        // General average: the average of the subjects' final grades.
        $generalAverage = $subjects->isEmpty() ? null : round($subjects->avg('final'), 2);

        return view('grades.report', compact('student', 'subjects', 'generalAverage'));
    }

    public function create(): View
    {
        return view('grades.create', [
            'grade' => new Grade,
            'students' => $this->students(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate($this->rules($request), $this->messages());

        Grade::create($data + ['teacher_id' => $request->user()->id]);

        return redirect()->route('grades.index')->with('success', 'Grade saved.');
    }

    public function edit(Grade $grade): View
    {
        Gate::authorize('update', $grade);

        return view('grades.edit', [
            'grade' => $grade,
            'students' => $this->students(),
        ]);
    }

    public function update(Request $request, Grade $grade): RedirectResponse
    {
        Gate::authorize('update', $grade);

        $grade->update($request->validate($this->rules($request, $grade), $this->messages()));

        return redirect()->route('grades.index')->with('success', 'Grade updated.');
    }

    public function destroy(Grade $grade): RedirectResponse
    {
        Gate::authorize('delete', $grade);

        $grade->delete();

        return redirect()->route('grades.index')->with('success', 'Grade deleted.');
    }

    private function students()
    {
        return User::where('role', 'student')->orderBy('name')->get();
    }

    private function rules(Request $request, ?Grade $grade = null): array
    {
        // One grade per student + subject + quarter. On update, the row being edited is ignored.
        $unique = Rule::unique('grades')
            ->where(fn ($query) => $query
                ->where('student_id', $request->student_id)
                ->where('subject', $request->subject))
            ->ignore($grade?->id);

        return [
            'student_id' => ['required', Rule::exists('users', 'id')->where('role', 'student')],
            'subject' => ['required', 'string', 'max:100'],
            'quarter' => ['required', 'integer', Rule::in(Grade::QUARTERS), $unique],
            'grade' => ['required', 'numeric', 'between:60,100'],
            'remarks' => ['nullable', 'string', 'max:255'],
        ];
    }

    private function messages(): array
    {
        return [
            'quarter.unique' => 'This student already has a grade for that subject and quarter.',
        ];
    }
}
