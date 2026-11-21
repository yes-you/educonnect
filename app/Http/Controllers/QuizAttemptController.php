<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class QuizAttemptController extends Controller
{
    // "My results": the logged-in student's own attempts.
    public function index(Request $request): View
    {
        $attempts = $request->user()->quizAttempts()
            ->with('quiz')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('attempts.index', compact('attempts'));
    }

    public function store(Request $request, Quiz $quiz): RedirectResponse
    {
        abort_unless($quiz->is_published && $quiz->grade_level === $request->user()->grade_level, 404);

        $questions = $quiz->questions;

        if ($questions->isEmpty()) {
            return back()->with('error', 'This quiz has no questions yet.');
        }

        // Every question must have an answer of a, b, c or d.
        $rules = ['answers' => ['required', 'array']];
        foreach ($questions as $question) {
            $rules['answers.'.$question->id] = ['required', Rule::in(Question::OPTIONS)];
        }
        $request->validate($rules);

        // The score is worked out here on the server, never in the browser.
        $answers = [];
        $score = 0;
        foreach ($questions as $question) {
            $choice = $request->input('answers.'.$question->id);
            $answers[$question->id] = $choice;
            if ($choice === $question->correct_option) {
                $score++;
            }
        }

        $attempt = QuizAttempt::create([
            'quiz_id' => $quiz->id,
            'user_id' => $request->user()->id,
            'score' => $score,
            'total' => $questions->count(),
            'answers' => $answers,
        ]);

        return redirect()->route('attempts.show', $attempt)
            ->with('success', 'You scored '.$score.' out of '.$questions->count().'.');
    }

    public function show(QuizAttempt $attempt): View
    {
        Gate::authorize('view', $attempt);

        $attempt->load(['quiz.questions', 'user']);

        return view('attempts.show', compact('attempt'));
    }
}
