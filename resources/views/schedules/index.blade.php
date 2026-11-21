@extends('layouts.main')

@section('title', 'Schedule')

@section('content')
    @php $user = auth()->user(); @endphp

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <h1 class="h3 mb-0">Class schedule</h1>
        @if ($user->hasRole('teacher', 'admin'))
            <a href="{{ route('schedules.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg" aria-hidden="true"></i> Add schedule
            </a>
        @endif
    </div>

    @if ($user->hasRole('teacher', 'admin'))
        <form method="GET" action="{{ route('schedules.index') }}" class="row g-2 align-items-end mb-3">
            <div class="col-md-4">
                <label for="grade_level" class="form-label">Grade level</label>
                <select id="grade_level" name="grade_level" class="form-select">
                    <option value="">All grades</option>
                    @foreach (\App\Models\User::GRADE_LEVELS as $level)
                        <option value="{{ $level }}" @selected(request('grade_level') === $level)>{{ $level }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-grid">
                <button type="submit" class="btn btn-outline-primary">Filter</button>
            </div>
        </form>
    @endif

    @php $hasAny = false; @endphp

    @foreach (\App\Models\Schedule::DAYS as $day)
        @if (isset($byDay[$day]))
            @php $hasAny = true; @endphp
            <section class="mb-4">
                <h2 class="h5">{{ $day }}</h2>
                <div class="table-responsive">
                    <table class="table table-sm align-middle">
                        <thead>
                            <tr>
                                <th scope="col">Time</th>
                                <th scope="col">Class</th>
                                <th scope="col">Subject</th>
                                <th scope="col">Grade</th>
                                <th scope="col">Room</th>
                                @if ($user->hasRole('teacher', 'admin'))
                                    <th scope="col"><span class="visually-hidden">Actions</span></th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($byDay[$day] as $schedule)
                                <tr>
                                    <td class="text-nowrap">{{ $schedule->start_time->format('g:i A') }} – {{ $schedule->end_time->format('g:i A') }}</td>
                                    <td>{{ $schedule->title }}</td>
                                    <td>{{ $schedule->subject }}</td>
                                    <td>{{ $schedule->grade_level }}</td>
                                    <td>{{ $schedule->room }}</td>
                                    @if ($user->hasRole('teacher', 'admin'))
                                        <td class="text-nowrap">
                                            @can('update', $schedule)
                                                <a href="{{ route('schedules.edit', $schedule) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                            @endcan
                                            @can('delete', $schedule)
                                                <form method="POST" action="{{ route('schedules.destroy', $schedule) }}" class="d-inline"
                                                      onsubmit="return confirm('Delete this class?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                                </form>
                                            @endcan
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        @endif
    @endforeach

    @unless ($hasAny)
        <p>No classes are scheduled yet.</p>
    @endunless
@endsection
