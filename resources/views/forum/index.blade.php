@extends('layouts.main')

@section('title', 'Forum')

@section('content')
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <h1 class="h3 mb-0">Discussion forum</h1>
        <a href="{{ route('threads.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg" aria-hidden="true"></i> New thread
        </a>
    </div>

    <form method="GET" action="{{ route('threads.index') }}" class="row g-2 align-items-end mb-3">
        <div class="col-md-6">
            <label for="q" class="form-label">Search threads by title</label>
            <input type="search" id="q" name="q" class="form-control" value="{{ request('q') }}">
        </div>
        <div class="col-md-2 d-grid">
            <button type="submit" class="btn btn-outline-primary">Search</button>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th scope="col">Thread</th>
                    <th scope="col">Started by</th>
                    <th scope="col">Replies</th>
                    <th scope="col">Posted</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($threads as $thread)
                    <tr>
                        <td><a href="{{ route('threads.show', $thread) }}">{{ $thread->title }}</a></td>
                        <td>{{ $thread->author->name }}</td>
                        <td>{{ $thread->replies_count }}</td>
                        <td>{{ $thread->created_at->format('M d, Y') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center py-4">No threads yet. Start the first one.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $threads->links() }}
@endsection
