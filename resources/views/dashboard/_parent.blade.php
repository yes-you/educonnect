@forelse ($children as $child)
    <section class="card mt-3" aria-labelledby="child-{{ $child->id }}">
        <div class="card-body">
            <h2 id="child-{{ $child->id }}" class="h5">
                {{ $child->name }} <span class="badge text-bg-light border">{{ $child->grade_level }}</span>
            </h2>

            <div class="row">
                <div class="col-md-6">
                    <h3 class="h6">Latest grades</h3>
                    @forelse ($child->latestGrades as $grade)
                        <p class="mb-1">{{ $grade->subject }} (Quarter {{ $grade->quarter }}): <strong>{{ $grade->grade }}</strong></p>
                    @empty
                        <p class="mb-0">No grades yet.</p>
                    @endforelse
                </div>

                <div class="col-md-6">
                    <h3 class="h6">Recent quiz results</h3>
                    @forelse ($child->latestAttempts as $attempt)
                        <p class="mb-1">
                            <a href="{{ route('attempts.show', $attempt) }}">{{ $attempt->quiz->title }}</a>
                            &middot; {{ $attempt->score }} / {{ $attempt->total }} ({{ $attempt->percentage() }}%)
                        </p>
                    @empty
                        <p class="mb-0">No quizzes taken yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </section>
@empty
    <div class="card mt-3">
        <div class="card-body">
            <p class="mb-0">No child is linked to your account yet. Please ask the school administrator to link your child.</p>
        </div>
    </div>
@endforelse

<p class="mt-2">
    <a href="{{ route('grades.index') }}">All grades</a> &middot;
    <a href="{{ route('schedules.index') }}">Class schedule</a>
</p>
