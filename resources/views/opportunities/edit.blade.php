@extends('layouts.main')

@section('title', 'Edit posting')

@section('content')
    <h1 class="h3 mb-3">Edit {{ $opportunity->title }}</h1>
    <p class="text-body-secondary">Saving your changes sends the posting back to the admin for approval.</p>

    <form method="POST" action="{{ route('opportunities.update', $opportunity) }}">
        @csrf
        @method('PATCH')
        @include('opportunities._form')

        <button type="submit" class="btn btn-primary">Save and resubmit</button>
        <a href="{{ route('opportunities.mine') }}" class="btn btn-outline-secondary">Cancel</a>
    </form>
@endsection
