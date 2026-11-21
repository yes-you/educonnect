@extends('layouts.main')

@section('title', 'Users')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Users</h1>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
            <i class="bi bi-person-plus" aria-hidden="true"></i> Create teacher or admin
        </a>
    </div>

    @include('partials.form-errors')

    <form method="GET" action="{{ route('admin.users.index') }}" class="row g-2 align-items-end mb-3">
        <div class="col-md-5">
            <label for="q" class="form-label">Search name or email</label>
            <input type="text" id="q" name="q" class="form-control" value="{{ request('q') }}">
        </div>
        <div class="col-md-3">
            <label for="role_filter" class="form-label">Role</label>
            <select id="role_filter" name="role" class="form-select">
                <option value="">All roles</option>
                @foreach (\App\Models\User::ROLES as $role)
                    <option value="{{ $role }}" @selected(request('role') === $role)>{{ ucfirst($role) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2 d-grid">
            <button type="submit" class="btn btn-outline-primary">Search</button>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th scope="col">Name</th>
                    <th scope="col">Email</th>
                    <th scope="col">Role</th>
                    <th scope="col">Grade level</th>
                    <th scope="col">Change role</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td><span class="badge text-bg-secondary">{{ $user->role }}</span></td>
                        <td>{{ $user->grade_level }}</td>
                        <td>
                            @if ($user->is(auth()->user()))
                                <span class="text-body-secondary">This is you</span>
                            @else
                                <form method="POST" action="{{ route('admin.users.role', $user) }}" class="d-flex gap-2 flex-wrap">
                                    @csrf
                                    @method('PATCH')
                                    <label for="role_{{ $user->id }}" class="visually-hidden">New role for {{ $user->name }}</label>
                                    <select id="role_{{ $user->id }}" name="role" class="form-select form-select-sm w-auto">
                                        @foreach (\App\Models\User::ROLES as $role)
                                            <option value="{{ $role }}" @selected($user->role === $role)>{{ ucfirst($role) }}</option>
                                        @endforeach
                                    </select>
                                    <label for="grade_{{ $user->id }}" class="visually-hidden">Grade level (students only) for {{ $user->name }}</label>
                                    <select id="grade_{{ $user->id }}" name="grade_level" class="form-select form-select-sm w-auto">
                                        <option value="">No grade level</option>
                                        @foreach (\App\Models\User::GRADE_LEVELS as $level)
                                            <option value="{{ $level }}" @selected($user->grade_level === $level)>{{ $level }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="btn btn-sm btn-outline-primary">Save</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center py-4">No users match your search.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $users->links() }}
@endsection
