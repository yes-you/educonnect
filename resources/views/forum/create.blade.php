@extends('layouts.main')

@section('title', 'New thread')

@section('content')
    <h1 class="h3 mb-3">New thread</h1>

    <form method="POST" action="{{ route('threads.store') }}">
        @csrf
        @include('partials.form-errors')

        <div class="mb-3">
            <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
            <input type="text" id="title" name="title" class="form-control @error('title') is-invalid @enderror"
                   value="{{ old('title') }}" required>
            @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label for="body" class="form-label">Your question or topic <span class="text-danger">*</span></label>
            <textarea id="body" name="body" rows="6" class="form-control @error('body') is-invalid @enderror" required>{{ old('body') }}</textarea>
            @error('body') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <button type="submit" class="btn btn-primary">Post thread</button>
        <a href="{{ route('threads.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </form>
@endsection
