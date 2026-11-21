@extends('layouts.main')

@section('title', $quiz->title)

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('quizzes.index') }}">Quizzes</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $quiz->title }}</li>
        </ol>
    </nav>

    <div class="card">
        <div class="card-body">
            <h1 class="h3">{{ $quiz->title }}</h1>
            <p class="mb-2">
                <span class="badge text-bg-info">{{ $quiz->subject }}</span>
                <span class="badge text-bg-light border">{{ $quiz->grade_level }}</span>
                @if ($quiz->is_published)
                    <span class="badge text-bg-success">Published</span>
                @else
                    <span class="badge text-bg-secondary">Draft</span>
                @endif
            </p>
            @if ($quiz->module)
                <p class="mb-2">Related module: <a href="{{ route('modules.show', $quiz->module) }}">{{ $quiz->module->title }}</a></p>
            @endif
            @if ($quiz->description)
                <p>{!! nl2br(e($quiz->description)) !!}</p>
            @endif
            <p class="text-body-secondary">{{ $quiz->questions->count() }} question(s), taken {{ $quiz->attempts_count }} time(s).</p>

            <form method="POST" action="{{ route('quizzes.publish', $quiz) }}" class="d-inline">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn {{ $quiz->is_published ? 'btn-outline-secondary' : 'btn-success' }}">
                    {{ $quiz->is_published ? 'Move back to draft' : 'Publish' }}
                </button>
            </form>

            @can('update', $quiz)
                <a href="{{ route('quizzes.edit', $quiz) }}" class="btn btn-outline-secondary">Edit details</a>
            @endcan

            @can('delete', $quiz)
                <form method="POST" action="{{ route('quizzes.destroy', $quiz) }}" class="d-inline"
                      onsubmit="return confirm('Delete this quiz and all its questions?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger">Delete</button>
                </form>
            @endcan
        </div>
    </div>

    <h2 class="h4 mt-4">Questions</h2>

    @forelse ($quiz->questions as $question)
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <h3 class="h6">{{ $loop->iteration }}. {{ $question->question }}</h3>
                    <form method="POST" action="{{ route('questions.destroy', $question) }}"
                          onsubmit="return confirm('Delete this question?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                    </form>
                </div>
                <ul class="list-unstyled mb-0">
                    @foreach (\App\Models\Question::OPTIONS as $option)
                        <li>
                            {{ strtoupper($option) }}. {{ $question->{'option_'.$option} }}
                            @if ($question->correct_option === $option)
                                <span class="badge text-bg-success">Correct answer</span>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    @empty
        <p>No questions yet. Add the first one below.</p>
    @endforelse

    <h2 class="h4 mt-4">Add a question</h2>

    <form method="POST" action="{{ route('questions.store', $quiz) }}">
        @csrf
        @include('partials.form-errors')

        <div class="mb-3">
            <label for="question" class="form-label">Question <span class="text-danger" aria-hidden="true">*</span></label>
            <textarea id="question" name="question" rows="2" class="form-control @error('question') is-invalid @enderror" required>{{ old('question') }}</textarea>
            @error('question') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="row">
            @foreach (\App\Models\Question::OPTIONS as $option)
                <div class="col-md-6 mb-3">
                    <label for="option_{{ $option }}" class="form-label">Option {{ strtoupper($option) }} <span class="text-danger" aria-hidden="true">*</span></label>
                    <input type="text" id="option_{{ $option }}" name="option_{{ $option }}"
                           class="form-control @error('option_'.$option) is-invalid @enderror"
                           value="{{ old('option_'.$option) }}" required>
                    @error('option_'.$option) <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            @endforeach
        </div>

        <div class="mb-3 col-md-4">
            <label for="correct_option" class="form-label">Correct answer <span class="text-danger" aria-hidden="true">*</span></label>
            <select id="correct_option" name="correct_option" class="form-select @error('correct_option') is-invalid @enderror" required>
                <option value="">Select</option>
                @foreach (\App\Models\Question::OPTIONS as $option)
                    <option value="{{ $option }}" @selected(old('correct_option') === $option)>{{ strtoupper($option) }}</option>
                @endforeach
            </select>
            @error('correct_option') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <button type="submit" class="btn btn-primary">Add question</button>
    </form>
@endsection
