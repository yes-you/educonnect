@extends('layouts.main')

@section('title', 'Quiz result')

@section('content')
    <h1 class="h3">{{ $attempt->quiz->title }}</h1>
    <p class="text-body-secondary">
        {{ $attempt->user->name }} &middot; {{ $attempt->created_at->format('M d, Y') }} {{ $attempt->created_at->format('g:i A') }}
    </p>

    <div class="card">
        <div class="card-body">
            <p class="h4 mb-0">Score: {{ $attempt->score }} / {{ $attempt->total }} ({{ $attempt->percentage() }}%)</p>
        </div>
    </div>

    <h2 class="h4 mt-4">Your answers</h2>

    @foreach ($attempt->quiz->questions as $question)
        @php
            $chosen = $attempt->answers[$question->id] ?? null;
            $isCorrect = $chosen === $question->correct_option;
        @endphp
        <div class="card">
            <div class="card-body">
                <h3 class="h6">
                    {{ $loop->iteration }}. {{ $question->question }}
                    @if ($isCorrect)
                        <i class="bi bi-check-circle-fill text-success" aria-hidden="true"></i>
                        <span class="visually-hidden">Correct</span>
                    @else
                        <i class="bi bi-x-circle-fill text-danger" aria-hidden="true"></i>
                        <span class="visually-hidden">Wrong</span>
                    @endif
                </h3>
                <ul class="list-unstyled mb-0">
                    @foreach (\App\Models\Question::OPTIONS as $option)
                        <li>
                            {{ strtoupper($option) }}. {{ $question->{'option_'.$option} }}
                            @if ($option === $chosen)
                                <span class="badge {{ $isCorrect ? 'text-bg-success' : 'text-bg-danger' }}">Your answer</span>
                            @endif
                            @if ($option === $question->correct_option)
                                <span class="badge text-bg-success">Correct answer</span>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endforeach

    <div class="mt-3">
        @if (auth()->user()->hasRole('student'))
            <a href="{{ route('attempts.index') }}" class="btn btn-outline-primary">My results</a>
        @endif
        <a href="{{ route('quizzes.index') }}" class="btn btn-outline-secondary">Back to quizzes</a>
    </div>
@endsection
