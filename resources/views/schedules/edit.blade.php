@extends('layouts.main')

@section('title', 'Edit schedule')

@section('content')
    <h1 class="h3 mb-3">Edit schedule</h1>

    <form method="POST" action="{{ route('schedules.update', $schedule) }}">
        @csrf
        @method('PATCH')
        @include('schedules._form')

        <button type="submit" class="btn btn-primary">Save changes</button>
        <a href="{{ route('schedules.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </form>
@endsection
