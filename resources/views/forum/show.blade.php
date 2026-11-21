@extends('layouts.main')

@section('title', $thread->title)

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('threads.index') }}">Forum</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $thread->title }}</li>
        </ol>
    </nav>

    <article class="card">
        <div class="card-body">
            <h1 class="h3">{{ $thread->title }}</h1>
            <p class="text-body-secondary small">
                {{ $thread->author->name }} &middot; {{ $thread->created_at->format('M d, Y') }} {{ $thread->created_at->format('g:i A') }}
            </p>
            <p class="mb-0">{!! nl2br(e($thread->body)) !!}</p>

            @can('delete', $thread)
                <form method="POST" action="{{ route('threads.destroy', $thread) }}" class="mt-3"
                      onsubmit="return confirm('Delete this thread and all its replies?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete thread</button>
                </form>
            @endcan
        </div>
    </article>

    <h2 class="h4 mt-4">Replies</h2>

    @forelse ($replies as $reply)
        <article class="card">
            <div class="card-body">
                <p class="text-body-secondary small mb-2">
                    {{ $reply->author->name }} &middot; {{ $reply->created_at->format('M d, Y') }} {{ $reply->created_at->format('g:i A') }}
                </p>
                <p class="mb-0">{!! nl2br(e($reply->body)) !!}</p>

                @can('delete', $reply)
                    <form method="POST" action="{{ route('replies.destroy', $reply) }}" class="mt-2"
                          onsubmit="return confirm('Delete this reply?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                    </form>
                @endcan
            </div>
        </article>
    @empty
        <p>No replies yet. Be the first to answer.</p>
    @endforelse

    {{ $replies->links() }}

    <h2 class="h4 mt-4">Add a reply</h2>

    <form method="POST" action="{{ route('replies.store', $thread) }}">
        @csrf
        @include('partials.form-errors')

        <div class="mb-3">
            <label for="body" class="form-label">Your reply <span class="text-danger">*</span></label>
            <textarea id="body" name="body" rows="4" class="form-control @error('body') is-invalid @enderror" required>{{ old('body') }}</textarea>
            @error('body') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <button type="submit" class="btn btn-primary">Post reply</button>
    </form>
@endsection
