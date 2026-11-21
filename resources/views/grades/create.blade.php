@extends('layouts.main')

@section('title', 'Encode grade')

@section('content')
    <h1 class="h3 mb-3">Encode grade</h1>

    <form method="POST" action="{{ route('grades.store') }}">
        @csrf
        @include('grades._form')

        <button type="submit" class="btn btn-primary">Save grade</button>
        <a href="{{ route('grades.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </form>
@endsection
