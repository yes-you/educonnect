<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class QuestionController extends Controller
{
    public function store(Request $request, Quiz $quiz): RedirectResponse
    {
        Gate::authorize('manage', $quiz);

        if ($quiz->attempts()->exists()) {
            return back()->with('error', 'Students already took this quiz. Make a new quiz instead of changing its questions.');
        }

        $data = $request->validate([
            'question' => ['required', 'string', 'max:1000'],
            'option_a' => ['required', 'string', 'max:255'],
            'option_b' => ['required', 'string', 'max:255'],
            'option_c' => ['required', 'string', 'max:255'],
            'option_d' => ['required', 'string', 'max:255'],
            'correct_option' => ['required', Rule::in(Question::OPTIONS)],
        ]);

        $quiz->questions()->create($data);

        return redirect()->route('quizzes.show', $quiz)->with('success', 'Question added.');
    }

    public function destroy(Question $question): RedirectResponse
    {
        $quiz = $question->quiz;

        Gate::authorize('manage', $quiz);

        if ($quiz->attempts()->exists()) {
            return back()->with('error', 'Students already took this quiz. Make a new quiz instead of changing its questions.');
        }

        $question->delete();

        return redirect()->route('quizzes.show', $quiz)->with('success', 'Question deleted.');
    }
}
