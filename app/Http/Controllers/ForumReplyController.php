<?php

namespace App\Http\Controllers;

use App\Models\ForumReply;
use App\Models\ForumThread;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ForumReplyController extends Controller
{
    public function store(Request $request, ForumThread $thread): RedirectResponse
    {
        $data = $request->validate([
            'body' => ['required', 'string', 'max:5000'],
        ]);

        $thread->replies()->create($data + ['user_id' => $request->user()->id]);

        return redirect()->route('threads.show', $thread)->with('success', 'Reply posted.');
    }

    public function destroy(ForumReply $reply): RedirectResponse
    {
        Gate::authorize('delete', $reply);

        $thread = $reply->thread;
        $reply->delete();

        return redirect()->route('threads.show', $thread)->with('success', 'Reply deleted.');
    }
}
