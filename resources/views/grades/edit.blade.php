@extends('layouts.main')

@section('title', 'Edit grade')

@section('content')
    <h1 class="h3 mb-3">Edit grade</h1>

    <form method="POST" action="{{ route('grades.update', $grade) }}">
        @csrf
        @method('PATCH')
        @include('grades._form')

        <button type="submit" class="btn btn-primary">Save changes</button>
        <a href="{{ route('grades.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </form>
@endsection
