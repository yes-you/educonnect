@extends('layouts.main')

@section('title', 'My results')

@section('content')
    <h1 class="h3 mb-3">My quiz results</h1>

    <div class="table-responsive">
        <table class="table table-hover align-middle ec-stack">
            <thead>
                <tr>
                    <th scope="col">Quiz</th>
                    <th scope="col">Score</th>
                    <th scope="col">Percentage</th>
                    <th scope="col">Date</th>
                    <th scope="col"><span class="visually-hidden">Details</span></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($attempts as $attempt)
                    <tr>
                        <td class="ec-stack-title">{{ $attempt->quiz->title }}</td>
                        <td data-label="Score">{{ $attempt->score }} / {{ $attempt->total }}</td>
                        <td data-label="Percentage">{{ $attempt->percentage() }}%</td>
                        <td data-label="Date">{{ $attempt->created_at->format('M d, Y') }} {{ $attempt->created_at->format('g:i A') }}</td>
                        <td><a href="{{ route('attempts.show', $attempt) }}">View answers</a></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4">
                            You have not taken any quiz yet. <a href="{{ route('quizzes.index') }}">Browse quizzes</a>.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $attempts->links() }}
@endsection
