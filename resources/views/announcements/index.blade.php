@extends('layouts.main')

@section('title', 'Announcements')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Announcements</h1>
        @if (auth()->user()->hasRole('teacher', 'admin'))
            <a href="{{ route('announcements.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg" aria-hidden="true"></i> New announcement
            </a>
        @endif
    </div>

    @forelse ($announcements as $announcement)
        <article class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <h2 class="h5">{{ $announcement->title }}</h2>
                    @if (auth()->user()->hasRole('teacher', 'admin'))
                        <span class="badge text-bg-secondary">For: {{ ucfirst($announcement->audience) }}</span>
                    @endif
                </div>
                <p class="text-body-secondary small">
                    {{ $announcement->author->name }} &middot; {{ $announcement->created_at->format('M d, Y') }} {{ $announcement->created_at->format('g:i A') }}
                </p>
                <p class="mb-0">{!! nl2br(e($announcement->body)) !!}</p>

                @can('delete', $announcement)
                    <form method="POST" action="{{ route('announcements.destroy', $announcement) }}" class="mt-3"
                          onsubmit="return confirm('Delete this announcement?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                    </form>
                @endcan
            </div>
        </article>
    @empty
        <p>No announcements yet.</p>
    @endforelse

    {{ $announcements->links() }}
@endsection
