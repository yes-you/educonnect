@extends('layouts.main')

@section('title', $group->name)

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('study-groups.index') }}">Study groups</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $group->name }}</li>
        </ol>
    </nav>

    <div class="card">
        <div class="card-body">
            <h1 class="h3">{{ $group->name }}</h1>
            @if ($group->subject)
                <p class="mb-2"><span class="badge text-bg-info">{{ $group->subject }}</span></p>
            @endif
            @if ($group->description)
                <p>{!! nl2br(e($group->description)) !!}</p>
            @endif
            <p class="text-body-secondary small mb-3">Owner: {{ $group->owner->name }}</p>

            <h2 class="h6">Members ({{ $group->members->count() }})</h2>
            <p class="mb-3">{{ $group->members->pluck('name')->join(', ') }}</p>

            @if ($isMember && $group->owner_id !== auth()->id())
                <form method="POST" action="{{ route('study-groups.leave', $group) }}" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-secondary">Leave group</button>
                </form>
            @endif

            @can('delete', $group)
                <form method="POST" action="{{ route('study-groups.destroy', $group) }}" class="d-inline"
                      onsubmit="return confirm('Delete this group and all its messages?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger">Delete group</button>
                </form>
            @endcan
        </div>
    </div>

    <h2 class="h4 mt-4">Messages</h2>

    @forelse ($messages as $message)
        <div class="card">
            <div class="card-body py-2">
                <p class="text-body-secondary small mb-1">
                    {{ $message->author->name }} &middot; {{ $message->created_at->format('M d, Y') }} {{ $message->created_at->format('g:i A') }}
                </p>
                <p class="mb-0">{!! nl2br(e($message->body)) !!}</p>
            </div>
        </div>
    @empty
        <p>No messages yet. Say hello.</p>
    @endforelse

    {{ $messages->links() }}

    @can('post', $group)
        <h2 class="h4 mt-4">Post a message</h2>

        <form method="POST" action="{{ route('group-messages.store', $group) }}">
            @csrf
            @include('partials.form-errors')

            <div class="mb-3">
                <label for="body" class="form-label">Message <span class="text-danger" aria-hidden="true">*</span></label>
                <textarea id="body" name="body" rows="3" class="form-control @error('body') is-invalid @enderror" required>{{ old('body') }}</textarea>
                @error('body') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <button type="submit" class="btn btn-primary">Post message</button>
        </form>
    @endcan
@endsection
