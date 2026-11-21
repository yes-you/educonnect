@extends('layouts.main')

@section('title', 'Add schedule')

@section('content')
    <h1 class="h3 mb-3">Add schedule</h1>

    <form method="POST" action="{{ route('schedules.store') }}">
        @csrf
        @include('schedules._form')

        <button type="submit" class="btn btn-primary">Save schedule</button>
        <a href="{{ route('schedules.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </form>
@endsection
