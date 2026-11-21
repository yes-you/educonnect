@extends('layouts.main')

@section('title', 'Grades')

@section('content')
    @php $user = auth()->user(); @endphp

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <h1 class="h3 mb-0">Grades</h1>
        @if ($user->hasRole('teacher', 'admin'))
            <a href="{{ route('grades.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg" aria-hidden="true"></i> Encode grade
            </a>
        @endif
    </div>

    @if ($user->hasRole('student'))
        <div class="card">
            <div class="card-body">
                @include('grades._table', ['grades' => $grades])
            </div>
        </div>

    @elseif ($user->hasRole('parent'))
        @forelse ($children as $child)
            <section class="card">
                <div class="card-body">
                    <h2 class="h5">{{ $child->name }} <span class="badge text-bg-light border">{{ $child->grade_level }}</span></h2>
                    @include('grades._table', ['grades' => $child->grades])
                </div>
            </section>
        @empty
            <p>No child is linked to your account yet. Please ask the school administrator to link your child.</p>
        @endforelse

    @else
        <form method="GET" action="{{ route('grades.index') }}" class="row g-2 align-items-end mb-3">
            <div class="col-md-4">
                <label for="student_id" class="form-label">Student</label>
                <select id="student_id" name="student_id" class="form-select">
                    <option value="">All students</option>
                    @foreach ($students as $student)
                        <option value="{{ $student->id }}" @selected((string) request('student_id') === (string) $student->id)>{{ $student->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label for="subject" class="form-label">Subject</label>
                <input type="text" id="subject" name="subject" class="form-control" value="{{ request('subject') }}">
            </div>
            <div class="col-md-2 d-grid">
                <button type="submit" class="btn btn-outline-primary">Filter</button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th scope="col">Student</th>
                        <th scope="col">Subject</th>
                        <th scope="col">Quarter</th>
                        <th scope="col">Grade</th>
                        <th scope="col">Remarks</th>
                        <th scope="col">Encoded by</th>
                        <th scope="col"><span class="visually-hidden">Actions</span></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($grades as $grade)
                        <tr>
                            <td>{{ $grade->student->name }}</td>
                            <td>{{ $grade->subject }}</td>
                            <td>{{ $grade->quarter }}</td>
                            <td>{{ $grade->grade }}</td>
                            <td>{{ $grade->remarks }}</td>
                            <td>{{ $grade->teacher->name }}</td>
                            <td class="text-nowrap">
                                @can('update', $grade)
                                    <a href="{{ route('grades.edit', $grade) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                @endcan
                                @can('delete', $grade)
                                    <form method="POST" action="{{ route('grades.destroy', $grade) }}" class="d-inline"
                                          onsubmit="return confirm('Delete this grade?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center py-4">No grades to show.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $grades->links() }}
    @endif
@endsection
