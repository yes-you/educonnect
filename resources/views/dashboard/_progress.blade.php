{{-- Quiz progress. Expects $progress from User::quizProgress(). Used on the student and parent dashboards. --}}
@if ($progress['attempts'] === 0)
    <p class="mb-0">No quizzes taken yet.</p>
@else
    <p class="mb-2">
        <strong>{{ $progress['attempts'] }}</strong> {{ \Illuminate\Support\Str::plural('attempt', $progress['attempts']) }}
        &middot; average score <strong>{{ $progress['average'] }}%</strong>
    </p>

    <div class="table-responsive">
        <table class="table table-sm align-middle mb-0">
            <thead>
                <tr>
                    <th scope="col">Quiz</th>
                    <th scope="col">Attempts</th>
                    <th scope="col">Average</th>
                    <th scope="col">Best score</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($progress['quizzes'] as $row)
                    <tr>
                        <th scope="row">{{ $row['quiz']->title }}</th>
                        <td>{{ $row['attempts'] }}</td>
                        <td>{{ $row['average'] }}%</td>
                        <td>{{ $row['best']->score }} / {{ $row['best']->total }} ({{ $row['best']->percentage() }}%)</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
