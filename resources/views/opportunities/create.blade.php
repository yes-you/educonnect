@extends('layouts.main')

@section('title', 'New posting')

@section('content')
    <h1 class="h3 mb-3">New posting</h1>
    <p class="text-body-secondary">The admin reviews every posting before it appears on the public page.</p>

    <form method="POST" action="{{ route('opportunities.store') }}">
        @csrf
        @include('opportunities._form')

        <button type="submit" class="btn btn-primary">Submit for approval</button>
        <a href="{{ route('opportunities.mine') }}" class="btn btn-outline-secondary">Cancel</a>
    </form>
@endsection
