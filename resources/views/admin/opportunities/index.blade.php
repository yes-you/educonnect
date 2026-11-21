@extends('layouts.main')

@section('title', 'Pending postings')

@section('content')
    <h1 class="h3 mb-3">Pending postings</h1>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th scope="col">Title</th>
                    <th scope="col">Type</th>
                    <th scope="col">Organization</th>
                    <th scope="col">Submitted</th>
                    <th scope="col"><span class="visually-hidden">Actions</span></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($opportunities as $opportunity)
                    <tr>
                        <td><a href="{{ route('opportunities.show', $opportunity) }}">{{ $opportunity->title }}</a></td>
                        <td>{{ ucfirst($opportunity->type) }}</td>
                        <td>{{ $opportunity->organization->name }}</td>
                        <td>{{ $opportunity->created_at->format('M d, Y') }}</td>
                        <td class="text-nowrap">
                            <form method="POST" action="{{ route('admin.opportunities.approve', $opportunity) }}" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-sm btn-success">Approve</button>
                            </form>
                            <form method="POST" action="{{ route('admin.opportunities.reject', $opportunity) }}" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Reject</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center py-4">No postings are waiting for review.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $opportunities->links() }}
@endsection
