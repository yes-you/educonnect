@extends('layouts.main')

@section('title', 'Opportunities')

@section('content')
    <h1 class="h3 mb-3">Community opportunities</h1>
    <p class="text-body-secondary">Scholarships, tutoring programs and events from local organizations.</p>

    <form method="GET" action="{{ route('opportunities.index') }}" class="row g-2 align-items-end mb-3">
        <div class="col-md-5">
            <label for="q" class="form-label">Search by title</label>
            <input type="search" id="q" name="q" class="form-control" value="{{ request('q') }}">
        </div>
        <div class="col-md-3">
            <label for="type" class="form-label">Type</label>
            <select id="type" name="type" class="form-select">
                <option value="">All types</option>
                @foreach (\App\Models\Opportunity::TYPES as $type)
                    <option value="{{ $type }}" @selected(request('type') === $type)>{{ ucfirst($type) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2 d-grid">
            <button type="submit" class="btn btn-outline-primary">Filter</button>
        </div>
    </form>

    @forelse ($opportunities as $opportunity)
        <article class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <h2 class="h5"><a href="{{ route('opportunities.show', $opportunity) }}">{{ $opportunity->title }}</a></h2>
                    <span class="badge text-bg-info">{{ ucfirst($opportunity->type) }}</span>
                </div>
                <p class="text-body-secondary small mb-2">
                    By {{ $opportunity->organization->name }}
                    @if ($opportunity->location) &middot; {{ $opportunity->location }} @endif
                    @if ($opportunity->event_date) &middot; Event: {{ $opportunity->event_date->format('M d, Y') }} @endif
                    @if ($opportunity->deadline) &middot; Deadline: {{ $opportunity->deadline->format('M d, Y') }} @endif
                </p>
                <p class="mb-0">{{ \Illuminate\Support\Str::limit($opportunity->description, 200) }}</p>
            </div>
        </article>
    @empty
        <p>No opportunities are listed right now. Please check back soon.</p>
    @endforelse

    {{ $opportunities->links() }}
@endsection
