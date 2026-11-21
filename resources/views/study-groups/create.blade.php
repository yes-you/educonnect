@extends('layouts.main')

@section('title', 'New study group')

@section('content')
    <h1 class="h3 mb-3">New study group</h1>
    <p class="text-muted">Study groups are open: any student or teacher can join, and members can read the group's messages.</p>

    <form method="POST" action="{{ route('study-groups.store') }}">
        @csrf
        @include('partials.form-errors')

        <div class="row">
            <div class="col-md-8 mb-3">
                <label for="name" class="form-label">Group name <span class="text-danger" aria-hidden="true">*</span></label>
                <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name') }}" required>
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-4 mb-3">
                <label for="subject" class="form-label">Subject</label>
                <input type="text" id="subject" name="subject" class="form-control @error('subject') is-invalid @enderror"
                       value="{{ old('subject') }}">
                @error('subject') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea id="description" name="description" rows="4" class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <button type="submit" class="btn btn-primary">Create group</button>
        <a href="{{ route('study-groups.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </form>
@endsection
