@extends('layouts.main')

@section('title', $opportunity->title)

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('opportunities.index') }}">Opportunities</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $opportunity->title }}</li>
        </ol>
    </nav>

    <div class="card">
        <div class="card-body">
            <h1 class="h3">{{ $opportunity->title }}</h1>
            <p class="mb-2">
                <span class="badge text-bg-info">{{ ucfirst($opportunity->type) }}</span>
                @if ($opportunity->status !== 'approved')
                    <span class="badge {{ $opportunity->status === 'pending' ? 'text-bg-warning' : 'text-bg-danger' }}">{{ ucfirst($opportunity->status) }}</span>
                @endif
            </p>
            <p class="text-body-secondary">Posted by {{ $opportunity->organization->name }}</p>

            <p>{!! nl2br(e($opportunity->description)) !!}</p>

            <dl class="mb-3">
                @if ($opportunity->location)
                    <dt>Location</dt>
                    <dd>{{ $opportunity->location }}</dd>
                @endif
                @if ($opportunity->event_date)
                    <dt>Event date</dt>
                    <dd>{{ $opportunity->event_date->format('M d, Y') }}</dd>
                @endif
                @if ($opportunity->deadline)
                    <dt>Application deadline</dt>
                    <dd>{{ $opportunity->deadline->format('M d, Y') }}</dd>
                @endif
                @if ($opportunity->link)
                    <dt>More information</dt>
                    <dd><a href="{{ $opportunity->link }}" target="_blank" rel="noopener noreferrer">{{ $opportunity->link }}</a></dd>
                @endif
            </dl>

            @auth
                @if (auth()->user()->hasRole('organization'))
                    @can('update', $opportunity)
                        <a href="{{ route('opportunities.edit', $opportunity) }}" class="btn btn-outline-secondary">Edit</a>
                    @endcan
                @endif

                @if (auth()->user()->hasRole('organization', 'admin'))
                    @can('delete', $opportunity)
                        <form method="POST" action="{{ route('opportunities.destroy', $opportunity) }}" class="d-inline"
                              onsubmit="return confirm('Delete this posting?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger">Delete</button>
                        </form>
                    @endcan
                @endif
            @endauth
        </div>
    </div>
@endsection
