@extends('layouts.main')

@section('title', $module->title)

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('modules.index') }}">Modules</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $module->title }}</li>
        </ol>
    </nav>

    <div class="card">
        <div class="card-body">
            <h1 class="h3">{{ $module->title }}</h1>
            <p class="mb-2">
                <span class="badge text-bg-secondary">{{ ucfirst(str_replace('_', ' ', $module->type)) }}</span>
                <span class="badge text-bg-info">{{ $module->subject }}</span>
                <span class="badge text-bg-light border">{{ $module->grade_level }}</span>
            </p>
            <p class="text-body-secondary">
                By {{ $module->teacher->name }} on {{ $module->created_at->format('M d, Y') }}
            </p>

            @if ($module->description)
                <p>{!! nl2br(e($module->description)) !!}</p>
            @endif

            <p class="mb-3">File: {{ $module->original_name }}</p>

            <a href="{{ route('modules.download', $module) }}" class="btn btn-primary">
                <i class="bi bi-download" aria-hidden="true"></i> Download
            </a>

            @can('update', $module)
                <a href="{{ route('modules.edit', $module) }}" class="btn btn-outline-secondary">Edit</a>
            @endcan

            @can('delete', $module)
                <form method="POST" action="{{ route('modules.destroy', $module) }}" class="d-inline"
                      onsubmit="return confirm('Delete this module?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger">Delete</button>
                </form>
            @endcan
        </div>
    </div>
@endsection
