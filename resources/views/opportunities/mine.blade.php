@extends('layouts.main')

@section('title', 'My postings')

@section('content')
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <h1 class="h3 mb-0">My postings</h1>
        <a href="{{ route('opportunities.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg" aria-hidden="true"></i> New posting
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th scope="col">Title</th>
                    <th scope="col">Type</th>
                    <th scope="col">Status</th>
                    <th scope="col">Submitted</th>
                    <th scope="col"><span class="visually-hidden">Actions</span></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($opportunities as $opportunity)
                    <tr>
                        <td><a href="{{ route('opportunities.show', $opportunity) }}">{{ $opportunity->title }}</a></td>
                        <td>{{ ucfirst($opportunity->type) }}</td>
                        <td>
                            @if ($opportunity->status === 'approved')
                                <span class="badge text-bg-success">Approved</span>
                            @elseif ($opportunity->status === 'pending')
                                <span class="badge text-bg-warning">Pending</span>
                            @else
                                <span class="badge text-bg-danger">Rejected</span>
                            @endif
                        </td>
                        <td>{{ $opportunity->created_at->format('M d, Y') }}</td>
                        <td class="text-nowrap">
                            <a href="{{ route('opportunities.edit', $opportunity) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                            <form method="POST" action="{{ route('opportunities.destroy', $opportunity) }}" class="d-inline"
                                  onsubmit="return confirm('Delete this posting?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center py-4">You have no postings yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $opportunities->links() }}
@endsection
