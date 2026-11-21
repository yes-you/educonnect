@extends('layouts.main')

@section('title', 'Upload module')

@section('content')
    <h1 class="h3 mb-3">Upload module or study guide</h1>

    <form method="POST" action="{{ route('modules.store') }}" enctype="multipart/form-data">
        @csrf
        @include('modules._form')

        <button type="submit" class="btn btn-primary">Upload</button>
        <a href="{{ route('modules.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </form>
@endsection
