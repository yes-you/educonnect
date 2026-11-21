<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ScheduleController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $query = Schedule::query();

        if ($user->hasRole('student')) {
            $query->where('grade_level', $user->grade_level);
        } elseif ($user->hasRole('parent')) {
            // The grade levels of the parent's linked children only.
            $query->whereIn('grade_level', $user->children()->pluck('grade_level')->unique());
        } elseif ($request->filled('grade_level')) {
            $query->where('grade_level', $request->grade_level);
        }

        // Grouped by day (Monday to Saturday), earliest class first. Not paginated: one week is a short list.
        $byDay = $query->orderBy('start_time')->get()->groupBy('day');

        return view('schedules.index', compact('byDay'));
    }

    public function create(): View
    {
        return view('schedules.create', ['schedule' => new Schedule]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate($this->rules());

        Schedule::create($data + ['user_id' => $request->user()->id]);

        return redirect()->route('schedules.index')->with('success', 'Schedule added.');
    }

    public function edit(Schedule $schedule): View
    {
        Gate::authorize('update', $schedule);

        return view('schedules.edit', compact('schedule'));
    }

    public function update(Request $request, Schedule $schedule): RedirectResponse
    {
        Gate::authorize('update', $schedule);

        $schedule->update($request->validate($this->rules()));

        return redirect()->route('schedules.index')->with('success', 'Schedule updated.');
    }

    public function destroy(Schedule $schedule): RedirectResponse
    {
        Gate::authorize('delete', $schedule);

        $schedule->delete();

        return redirect()->route('schedules.index')->with('success', 'Schedule deleted.');
    }

    private function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'subject' => ['required', 'string', 'max:100'],
            'grade_level' => ['required', Rule::in(User::GRADE_LEVELS)],
            'day' => ['required', Rule::in(Schedule::DAYS)],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'room' => ['nullable', 'string', 'max:50'],
        ];
    }
}
