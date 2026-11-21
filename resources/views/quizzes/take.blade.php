@extends('layouts.main')

@section('title', $quiz->title)

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('quizzes.index') }}">Quizzes</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $quiz->title }}</li>
        </ol>
    </nav>

    <h1 class="h3">{{ $quiz->title }}</h1>
    <p class="text-body-secondary">{{ $quiz->subject }} &middot; {{ $quiz->grade_level }} &middot; {{ $questions->count() }} question(s)</p>
    @if ($quiz->description)
        <p>{!! nl2br(e($quiz->description)) !!}</p>
    @endif

    <form method="POST" action="{{ route('attempts.store', $quiz) }}">
        @csrf
        @include('partials.form-errors')

        @foreach ($questions as $question)
            <fieldset class="card card-body mb-3">
                <legend class="h6 float-none">{{ $loop->iteration }}. {{ $question->question }}</legend>

                @foreach (\App\Models\Question::OPTIONS as $option)
                    <div class="form-check">
                        <input class="form-check-input @error('answers.'.$question->id) is-invalid @enderror" type="radio"
                               name="answers[{{ $question->id }}]" id="q{{ $question->id }}_{{ $option }}" value="{{ $option }}"
                               @checked(old('answers.'.$question->id) === $option) required>
                        <label class="form-check-label" for="q{{ $question->id }}_{{ $option }}">
                            {{ strtoupper($option) }}. {{ $question->{'option_'.$option} }}
                        </label>
                    </div>
                @endforeach
            </fieldset>
        @endforeach

        <button type="submit" class="btn btn-primary">Submit answers</button>
        <a href="{{ route('quizzes.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </form>
@endsection
