<?php

namespace App\Http\Controllers;

use App\Models\Opportunity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class OpportunityController extends Controller
{
    // Public: approved postings only.
    public function index(Request $request): View
    {
        // Order: upcoming dates first (soonest on top), then postings without a date, then past ones.
        $date = 'COALESCE(event_date, deadline)';
        $order = "CASE WHEN $date IS NULL THEN 1 WHEN $date >= ? THEN 0 ELSE 2 END, $date ASC";

        $opportunities = Opportunity::approved()
            ->with('organization')
            ->when($request->filled('q'), fn ($query) => $query->where('title', 'like', '%'.$request->q.'%'))
            ->when($request->filled('type'), fn ($query) => $query->where('type', $request->type))
            ->orderByRaw($order, [today()->toDateString()])
            ->paginate(10)
            ->withQueryString();

        return view('opportunities.index', compact('opportunities'));
    }

    // Public for approved postings. Pending and rejected ones are visible only to the owner and the admin.
    public function show(Request $request, Opportunity $opportunity): View
    {
        $user = $request->user();
        $mayViewUnapproved = $user && ($user->hasRole('admin') || $user->id === $opportunity->user_id);

        abort_unless($opportunity->status === 'approved' || $mayViewUnapproved, 404);

        $opportunity->load('organization');

        return view('opportunities.show', compact('opportunity'));
    }

    // Organization: its own postings with status badges.
    public function mine(Request $request): View
    {
        $opportunities = $request->user()->opportunities()
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('opportunities.mine', compact('opportunities'));
    }

    public function create(): View
    {
        return view('opportunities.create', ['opportunity' => new Opportunity]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate($this->rules());

        // Status is never taken from the form: every new posting waits for the admin.
        Opportunity::create($data + [
            'user_id' => $request->user()->id,
            'status' => 'pending',
        ]);

        return redirect()->route('opportunities.mine')
            ->with('success', 'Posting submitted. It will appear on the public page after the admin approves it.');
    }

    public function edit(Opportunity $opportunity): View
    {
        Gate::authorize('update', $opportunity);

        return view('opportunities.edit', compact('opportunity'));
    }

    public function update(Request $request, Opportunity $opportunity): RedirectResponse
    {
        Gate::authorize('update', $opportunity);

        // Any edit sends the posting back to the admin for a new review.
        $opportunity->update($request->validate($this->rules()) + ['status' => 'pending']);

        return redirect()->route('opportunities.mine')
            ->with('success', 'Posting updated. It is pending approval again.');
    }

    public function destroy(Request $request, Opportunity $opportunity): RedirectResponse
    {
        Gate::authorize('delete', $opportunity);

        $opportunity->delete();

        $back = $request->user()->hasRole('admin') ? 'admin.opportunities.index' : 'opportunities.mine';

        return redirect()->route($back)->with('success', 'Posting deleted.');
    }

    private function rules(): array
    {
        return [
            'type' => ['required', Rule::in(Opportunity::TYPES)],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
            'location' => ['nullable', 'string', 'max:255'],
            'event_date' => ['nullable', 'date'],
            'deadline' => ['nullable', 'date'],
            'link' => ['nullable', 'url:http,https', 'max:255'],
        ];
    }
}
