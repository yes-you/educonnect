@extends('layouts.main')

@section('title', 'Edit module')

@section('content')
    <h1 class="h3 mb-3">Edit {{ $module->title }}</h1>

    <form method="POST" action="{{ route('modules.update', $module) }}" enctype="multipart/form-data">
        @csrf
        @method('PATCH')
        @include('modules._form')

        <button type="submit" class="btn btn-primary">Save changes</button>
        <a href="{{ route('modules.show', $module) }}" class="btn btn-outline-secondary">Cancel</a>
    </form>
@endsection
