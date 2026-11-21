@extends('layouts.main')

@section('title', 'Create quiz')

@section('content')
    <h1 class="h3 mb-3">Create quiz</h1>

    <form method="POST" action="{{ route('quizzes.store') }}">
        @csrf
        @include('quizzes._form')

        <button type="submit" class="btn btn-primary">Create quiz</button>
        <a href="{{ route('quizzes.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </form>
@endsection
