@extends('layouts.main')

@section('title', 'New announcement')

@section('content')
    <h1 class="h3 mb-3">New announcement</h1>

    <form method="POST" action="{{ route('announcements.store') }}">
        @csrf
        @include('partials.form-errors')

        <div class="mb-3">
            <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
            <input type="text" id="title" name="title" class="form-control @error('title') is-invalid @enderror"
                   value="{{ old('title') }}" required>
            @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3 col-md-4">
            <label for="audience" class="form-label">Who is it for? <span class="text-danger">*</span></label>
            <select id="audience" name="audience" class="form-select @error('audience') is-invalid @enderror" required>
                @foreach (\App\Models\Announcement::AUDIENCES as $audience)
                    <option value="{{ $audience }}" @selected(old('audience', 'all') === $audience)>{{ ucfirst($audience) }}</option>
                @endforeach
            </select>
            @error('audience') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label for="body" class="form-label">Message <span class="text-danger">*</span></label>
            <textarea id="body" name="body" rows="6" class="form-control @error('body') is-invalid @enderror" required>{{ old('body') }}</textarea>
            @error('body') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <button type="submit" class="btn btn-primary">Post announcement</button>
        <a href="{{ route('announcements.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </form>
@endsection
