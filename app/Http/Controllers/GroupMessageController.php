<?php

namespace App\Http\Controllers;

use App\Models\StudyGroup;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class GroupMessageController extends Controller
{
    public function store(Request $request, StudyGroup $studyGroup): RedirectResponse
    {
        // Only members may post.
        Gate::authorize('post', $studyGroup);

        $data = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
        ]);

        $studyGroup->messages()->create($data + ['user_id' => $request->user()->id]);

        return redirect()->route('study-groups.show', $studyGroup)->with('success', 'Message posted.');
    }
}
