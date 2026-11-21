@extends('layouts.main')

@section('title', 'Create account')

@section('content')
    <h1 class="h3 mb-3">Create teacher or admin account</h1>

    <form method="POST" action="{{ route('admin.users.store') }}">
        @csrf
        @include('partials.form-errors')

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="name" class="form-label">Name <span class="text-danger" aria-hidden="true">*</span></label>
                <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name') }}" required>
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6 mb-3">
                <label for="email" class="form-label">Email <span class="text-danger" aria-hidden="true">*</span></label>
                <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email') }}" required>
                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="mb-3 col-md-4">
            <label for="role" class="form-label">Role <span class="text-danger" aria-hidden="true">*</span></label>
            <select id="role" name="role" class="form-select @error('role') is-invalid @enderror" required>
                @foreach (\App\Models\User::STAFF_ROLES as $role)
                    <option value="{{ $role }}" @selected(old('role', 'teacher') === $role)>{{ ucfirst($role) }}</option>
                @endforeach
            </select>
            @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="password" class="form-label">Password <span class="text-danger" aria-hidden="true">*</span></label>
                <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror"
                       autocomplete="new-password" required>
                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6 mb-3">
                <label for="password_confirmation" class="form-label">Confirm password <span class="text-danger" aria-hidden="true">*</span></label>
                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control"
                       autocomplete="new-password" required>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Create account</button>
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </form>
@endsection
