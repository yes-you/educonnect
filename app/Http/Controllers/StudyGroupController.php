<?php

namespace App\Http\Controllers;

use App\Models\StudyGroup;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class StudyGroupController extends Controller
{
    public function index(Request $request): View
    {
        $groups = StudyGroup::with('owner')
            ->withCount('members')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        // Ids of the groups the logged-in user already belongs to, for the "Joined" badge.
        $joinedIds = $request->user()->studyGroups()->pluck('study_groups.id')->all();

        return view('study-groups.index', compact('groups', 'joinedIds'));
    }

    public function create(): View
    {
        return view('study-groups.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'subject' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:5000'],
        ]);

        $group = StudyGroup::create($data + ['owner_id' => $request->user()->id]);

        // The owner is the first member.
        $group->members()->attach($request->user()->id);

        return redirect()->route('study-groups.show', $group)->with('success', 'Study group created.');
    }

    public function show(Request $request, StudyGroup $studyGroup): View
    {
        Gate::authorize('view', $studyGroup);

        $studyGroup->load('owner', 'members');

        $messages = $studyGroup->messages()
            ->with('author')
            ->oldest()
            ->paginate(20)
            ->withQueryString();

        $isMember = $studyGroup->hasMember($request->user());

        return view('study-groups.show', ['group' => $studyGroup, 'messages' => $messages, 'isMember' => $isMember]);
    }

    public function destroy(StudyGroup $studyGroup): RedirectResponse
    {
        Gate::authorize('delete', $studyGroup);

        $studyGroup->delete();

        return redirect()->route('study-groups.index')->with('success', 'Study group deleted.');
    }

    public function join(Request $request, StudyGroup $studyGroup): RedirectResponse
    {
        // syncWithoutDetaching never adds the same member twice.
        $studyGroup->members()->syncWithoutDetaching([$request->user()->id]);

        return redirect()->route('study-groups.show', $studyGroup)->with('success', 'You joined the group.');
    }

    public function leave(Request $request, StudyGroup $studyGroup): RedirectResponse
    {
        if ($studyGroup->owner_id === $request->user()->id) {
            return back()->with('error', 'The owner cannot leave the group. Delete the group instead.');
        }

        $studyGroup->members()->detach($request->user()->id);

        return redirect()->route('study-groups.index')->with('success', 'You left the group.');
    }
}
