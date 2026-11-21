<?php

namespace App\Http\Controllers;

use App\Models\ForumThread;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ForumThreadController extends Controller
{
    public function index(Request $request): View
    {
        $threads = ForumThread::with('author')
            ->withCount('replies')
            ->when($request->filled('q'), fn ($query) => $query->whereLike('title', '%'.$request->q.'%'))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('forum.index', compact('threads'));
    }

    public function create(): View
    {
        return view('forum.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:5000'],
        ]);

        $thread = ForumThread::create($data + ['user_id' => $request->user()->id]);

        return redirect()->route('threads.show', $thread)->with('success', 'Thread posted.');
    }

    public function show(ForumThread $thread): View
    {
        $thread->load('author');

        // Replies read like a conversation: oldest first.
        $replies = $thread->replies()
            ->with('author')
            ->oldest()
            ->paginate(20)
            ->withQueryString();

        return view('forum.show', compact('thread', 'replies'));
    }

    public function destroy(ForumThread $thread): RedirectResponse
    {
        Gate::authorize('delete', $thread);

        $thread->delete();

        return redirect()->route('threads.index')->with('success', 'Thread deleted.');
    }
}
