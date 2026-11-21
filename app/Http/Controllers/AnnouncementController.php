<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AnnouncementController extends Controller
{
    public function index(Request $request): View
    {
        // Each role only sees the announcements meant for it (see Announcement::scopeVisibleTo).
        $announcements = Announcement::with('author')
            ->visibleTo($request->user())
            ->latest()
            ->paginate(10)
            ->withQueryString();

        // Opening this page marks everything as seen, so the navbar badge goes back to zero.
        $request->user()->forceFill(['announcements_seen_at' => now()])->save();

        return view('announcements.index', compact('announcements'));
    }

    public function create(): View
    {
        return view('announcements.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:5000'],
            'audience' => ['required', Rule::in(Announcement::AUDIENCES)],
        ]);

        Announcement::create($data + ['user_id' => $request->user()->id]);

        return redirect()->route('announcements.index')->with('success', 'Announcement posted.');
    }

    public function destroy(Announcement $announcement): RedirectResponse
    {
        Gate::authorize('delete', $announcement);

        $announcement->delete();

        return redirect()->route('announcements.index')->with('success', 'Announcement deleted.');
    }
}
