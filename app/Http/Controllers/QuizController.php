<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\Quiz;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class QuizController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $quizzes = Quiz::with('teacher')
            ->withCount('questions')
            ->when($user->hasRole('student'), fn ($query) => $query->where('is_published', true))
            ->when($user->hasRole('student'), fn ($query) => $query->where('grade_level', $user->grade_level))
            ->when($user->hasRole('teacher'), fn ($query) => $query->where('teacher_id', $user->id))
            ->when($request->filled('grade_level') && ! $user->hasRole('student'), fn ($query) => $query->where('grade_level', $request->grade_level))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        // A student's own attempts by quiz id, so the list can show their result next to each quiz.
        $attempts = $user->hasRole('student') ? $user->quizAttempts()->get()->keyBy('quiz_id') : collect();

        return view('quizzes.index', compact('quizzes', 'attempts'));
    }

    public function show(Request $request, Quiz $quiz): View|RedirectResponse
    {
        if ($request->user()->hasRole('student')) {
            // Unpublished quizzes and quizzes of another grade level do not exist for a student.
            abort_unless($quiz->is_published && $quiz->grade_level === $request->user()->grade_level, 404);

            // A quiz can be attempted only once: if it is already taken, show the result instead.
            if ($attempt = $quiz->attemptBy($request->user())) {
                return redirect()->route('attempts.show', $attempt)
                    ->with('error', 'You have already taken this quiz. Here is your result.');
            }

            // Leave correct_option out of the query so it can never reach the page.
            $questions = $quiz->questions()
                ->select('id', 'quiz_id', 'question', 'option_a', 'option_b', 'option_c', 'option_d')
                ->get();

            return view('quizzes.take', compact('quiz', 'questions'));
        }

        Gate::authorize('manage', $quiz);

        $quiz->load(['questions', 'module'])->loadCount('attempts');

        return view('quizzes.manage', compact('quiz'));
    }

    public function create(Request $request): View
    {
        return view('quizzes.create', [
            'quiz' => new Quiz,
            'modules' => $this->availableModules($request->user()),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate($this->rules($request->user()));

        $quiz = Quiz::create($data + ['teacher_id' => $request->user()->id]);

        return redirect()->route('quizzes.show', $quiz)->with('success', 'Quiz created. Now add its questions.');
    }

    public function edit(Request $request, Quiz $quiz): View
    {
        Gate::authorize('update', $quiz);

        return view('quizzes.edit', [
            'quiz' => $quiz,
            'modules' => $this->availableModules($request->user()),
        ]);
    }

    public function update(Request $request, Quiz $quiz): RedirectResponse
    {
        Gate::authorize('update', $quiz);

        $quiz->update($request->validate($this->rules($request->user())));

        return redirect()->route('quizzes.show', $quiz)->with('success', 'Quiz updated.');
    }

    public function destroy(Quiz $quiz): RedirectResponse
    {
        Gate::authorize('delete', $quiz);

        $quiz->delete();

        return redirect()->route('quizzes.index')->with('success', 'Quiz deleted.');
    }

    public function publish(Quiz $quiz): RedirectResponse
    {
        Gate::authorize('manage', $quiz);

        if (! $quiz->is_published && $quiz->questions()->count() === 0) {
            return back()->with('error', 'Add at least one question before publishing.');
        }

        $quiz->update(['is_published' => ! $quiz->is_published]);

        return back()->with('success', $quiz->is_published ? 'Quiz published.' : 'Quiz moved back to draft.');
    }

    private function rules(User $user): array
    {
        // A teacher may link only their own modules; an admin may link any.
        $moduleExists = Rule::exists('modules', 'id');
        if (! $user->hasRole('admin')) {
            $moduleExists = $moduleExists->where('teacher_id', $user->id);
        }

        return [
            'title' => ['required', 'string', 'max:255'],
            'subject' => ['required', 'string', 'max:100'],
            'grade_level' => ['required', Rule::in(User::GRADE_LEVELS)],
            'module_id' => ['nullable', $moduleExists],
            'description' => ['nullable', 'string', 'max:5000'],
        ];
    }

    private function availableModules(User $user)
    {
        return $user->hasRole('admin')
            ? Module::orderBy('title')->get()
            : $user->modules()->orderBy('title')->get();
    }
}
