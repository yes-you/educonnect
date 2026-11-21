@extends('layouts.main')

@section('title', 'Modules')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Modules and study guides</h1>
        @if (auth()->user()->hasRole('teacher', 'admin'))
            <a href="{{ route('modules.create') }}" class="btn btn-primary">
                <i class="bi bi-upload" aria-hidden="true"></i> Upload module
            </a>
        @endif
    </div>

    <form method="GET" action="{{ route('modules.index') }}" class="row g-2 align-items-end mb-3">
        <div class="col-md-4">
            <label for="subject" class="form-label">Subject</label>
            <input type="text" id="subject" name="subject" class="form-control" value="{{ request('subject') }}">
        </div>
        @unless (auth()->user()->hasRole('student'))
            <div class="col-md-3">
                <label for="grade_level" class="form-label">Grade level</label>
                <select id="grade_level" name="grade_level" class="form-select">
                    <option value="">All grades</option>
                    @foreach (\App\Models\User::GRADE_LEVELS as $level)
                        <option value="{{ $level }}" @selected($gradeLevel === $level)>{{ $level }}</option>
                    @endforeach
                </select>
            </div>
        @endunless
        <div class="col-md-3">
            <label for="type" class="form-label">Type</label>
            <select id="type" name="type" class="form-select">
                <option value="">All types</option>
                @foreach (\App\Models\Module::TYPES as $type)
                    <option value="{{ $type }}" @selected(request('type') === $type)>{{ ucfirst(str_replace('_', ' ', $type)) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2 d-grid">
            <button type="submit" class="btn btn-outline-primary">Filter</button>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th scope="col">Title</th>
                    <th scope="col">Subject</th>
                    <th scope="col">Grade</th>
                    <th scope="col">Type</th>
                    <th scope="col">Teacher</th>
                    <th scope="col">Uploaded</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($modules as $module)
                    <tr>
                        <td><a href="{{ route('modules.show', $module) }}">{{ $module->title }}</a></td>
                        <td>{{ $module->subject }}</td>
                        <td>{{ $module->grade_level }}</td>
                        <td><span class="badge text-bg-secondary">{{ ucfirst(str_replace('_', ' ', $module->type)) }}</span></td>
                        <td>{{ $module->teacher->name }}</td>
                        <td>{{ $module->created_at->format('M d, Y') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center py-4">No modules match your filters.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $modules->links() }}
@endsection
