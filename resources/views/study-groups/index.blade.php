@extends('layouts.main')

@section('title', 'Study groups')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Study groups</h1>
        <a href="{{ route('study-groups.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg" aria-hidden="true"></i> New group
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th scope="col">Group</th>
                    <th scope="col">Subject</th>
                    <th scope="col">Owner</th>
                    <th scope="col">Members</th>
                    <th scope="col"><span class="visually-hidden">Join or joined</span></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($groups as $group)
                    <tr>
                        <td><a href="{{ route('study-groups.show', $group) }}">{{ $group->name }}</a></td>
                        <td>{{ $group->subject }}</td>
                        <td>{{ $group->owner->name }}</td>
                        <td>{{ $group->members_count }}</td>
                        <td>
                            @if (in_array($group->id, $joinedIds))
                                <span class="badge text-bg-success">Joined</span>
                            @else
                                <form method="POST" action="{{ route('study-groups.join', $group) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-primary">Join</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center py-4">No study groups yet. Create the first one.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $groups->links() }}
@endsection
