@extends('layouts.main')

@section('title', 'Edit quiz')

@section('content')
    <h1 class="h3 mb-3">Edit {{ $quiz->title }}</h1>

    <form method="POST" action="{{ route('quizzes.update', $quiz) }}">
        @csrf
        @method('PATCH')
        @include('quizzes._form')

        <button type="submit" class="btn btn-primary">Save changes</button>
        <a href="{{ route('quizzes.show', $quiz) }}" class="btn btn-outline-secondary">Cancel</a>
    </form>
@endsection
