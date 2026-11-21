@extends('layouts.main')

@section('title', 'Dashboard')

@section('content')
    <h1 class="h3 mb-3">Dashboard</h1>

    <div class="card">
        <div class="card-body d-flex align-items-center gap-3">
            <span class="ec-icon mb-0"><i class="bi bi-mortarboard" aria-hidden="true"></i></span>
            <p class="card-text mb-0">
                Welcome, <strong>{{ auth()->user()->name }}</strong>
                <span class="badge text-bg-secondary">{{ auth()->user()->role }}</span>
            </p>
        </div>
    </div>

    {{-- One partial per role: _admin, _teacher, _student, _parent, _organization --}}
    @include('dashboard._'.auth()->user()->role, $data)

    <section aria-labelledby="latest-announcements">
        <h2 id="latest-announcements" class="h4 mt-4">Latest announcements</h2>

        @forelse ($announcements as $announcement)
            <article class="card">
                <div class="card-body">
                    <h3 class="h6">{{ $announcement->title }}</h3>
                    <p class="text-body-secondary small mb-2">
                        {{ $announcement->author->name }} &middot; {{ $announcement->created_at->format('M d, Y') }}
                    </p>
                    <p class="mb-0">{{ \Illuminate\Support\Str::limit($announcement->body, 160) }}</p>
                </div>
            </article>
        @empty
            <p>No announcements yet.</p>
        @endforelse

        <a href="{{ route('announcements.index') }}">See all announcements</a>
    </section>
@endsection
