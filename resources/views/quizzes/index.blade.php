@extends('layouts.main')

@section('title', 'Quizzes')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Quizzes</h1>
        <div>
            @if (auth()->user()->hasRole('student'))
                <a href="{{ route('attempts.index') }}" class="btn btn-outline-primary">My results</a>
            @endif
            @if (auth()->user()->hasRole('teacher', 'admin'))
                <a href="{{ route('quizzes.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg" aria-hidden="true"></i> Create quiz
                </a>
            @endif
        </div>
    </div>

    @unless (auth()->user()->hasRole('student'))
        <form method="GET" action="{{ route('quizzes.index') }}" class="row g-2 align-items-end mb-3">
            <div class="col-md-4">
                <label for="grade_level" class="form-label">Grade level</label>
                <select id="grade_level" name="grade_level" class="form-select">
                    <option value="">All grades</option>
                    @foreach (\App\Models\User::GRADE_LEVELS as $level)
                        <option value="{{ $level }}" @selected(request('grade_level') === $level)>{{ $level }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-grid">
                <button type="submit" class="btn btn-outline-primary">Filter</button>
            </div>
        </form>
    @endunless

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th scope="col">Title</th>
                    <th scope="col">Subject</th>
                    <th scope="col">Grade</th>
                    <th scope="col">Questions</th>
                    <th scope="col">Teacher</th>
                    @unless (auth()->user()->hasRole('student'))
                        <th scope="col">Status</th>
                    @endunless
                </tr>
            </thead>
            <tbody>
                @forelse ($quizzes as $quiz)
                    <tr>
                        <td><a href="{{ route('quizzes.show', $quiz) }}">{{ $quiz->title }}</a></td>
                        <td>{{ $quiz->subject }}</td>
                        <td>{{ $quiz->grade_level }}</td>
                        <td>{{ $quiz->questions_count }}</td>
                        <td>{{ $quiz->teacher->name }}</td>
                        @unless (auth()->user()->hasRole('student'))
                            <td>
                                @if ($quiz->is_published)
                                    <span class="badge text-bg-success">Published</span>
                                @else
                                    <span class="badge text-bg-secondary">Draft</span>
                                @endif
                            </td>
                        @endunless
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center py-4">No quizzes to show.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $quizzes->links() }}
@endsection
