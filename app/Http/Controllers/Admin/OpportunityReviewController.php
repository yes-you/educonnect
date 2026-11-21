<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Opportunity;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class OpportunityReviewController extends Controller
{
    // Postings waiting for a decision, oldest first.
    public function index(): View
    {
        $opportunities = Opportunity::with('organization')
            ->where('status', 'pending')
            ->oldest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.opportunities.index', compact('opportunities'));
    }

    public function approve(Opportunity $opportunity): RedirectResponse
    {
        $opportunity->update(['status' => 'approved']);

        return redirect()->route('admin.opportunities.index')->with('success', 'Posting approved.');
    }

    public function reject(Opportunity $opportunity): RedirectResponse
    {
        $opportunity->update(['status' => 'rejected']);

        return redirect()->route('admin.opportunities.index')->with('success', 'Posting rejected.');
    }
}
