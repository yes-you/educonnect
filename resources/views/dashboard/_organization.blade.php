<section class="card mt-3" aria-labelledby="org-postings">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h2 id="org-postings" class="h5 mb-0">My postings</h2>
            <a href="{{ route('opportunities.create') }}" class="btn btn-primary btn-sm">New posting</a>
        </div>

        @forelse ($opportunities as $opportunity)
            <p class="mb-1">
                <a href="{{ route('opportunities.show', $opportunity) }}">{{ $opportunity->title }}</a>
                <span class="text-body-secondary small">&middot; {{ ucfirst($opportunity->type) }}</span>
                @if ($opportunity->status === 'approved')
                    <span class="badge text-bg-success">Approved</span>
                @elseif ($opportunity->status === 'pending')
                    <span class="badge text-bg-warning">Pending</span>
                @else
                    <span class="badge text-bg-danger">Rejected</span>
                @endif
            </p>
        @empty
            <p class="mb-0">You have not posted anything yet.</p>
        @endforelse

        <a href="{{ route('opportunities.mine') }}" class="d-inline-block mt-2">Manage my postings</a>
    </div>
</section>
