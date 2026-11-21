@php $grade = auth()->user()->grade_level; @endphp

<div class="row mt-3">
    <div class="col-lg-6">
        <section class="card h-100" aria-labelledby="student-modules">
            <div class="card-body">
                <h2 id="student-modules" class="h5">Latest modules for {{ $grade }}</h2>
                @forelse ($modules as $module)
                    <p class="mb-1">
                        <a href="{{ route('modules.show', $module) }}">{{ $module->title }}</a>
                        <span class="text-body-secondary small">&middot; {{ $module->subject }}</span>
                    </p>
                @empty
                    <p class="mb-0">No modules for your grade yet.</p>
                @endforelse
                <a href="{{ route('modules.index') }}" class="d-inline-block mt-2">All modules</a>
            </div>
        </section>
    </div>

    <div class="col-lg-6">
        <section class="card h-100" aria-labelledby="student-quizzes">
            <div class="card-body">
                <h2 id="student-quizzes" class="h5">Quizzes to take</h2>
                @forelse ($quizzes as $quiz)
                    <p class="mb-1">
                        <a href="{{ route('quizzes.show', $quiz) }}">{{ $quiz->title }}</a>
                        <span class="text-body-secondary small">&middot; {{ $quiz->questions_count }} questions</span>
                    </p>
                @empty
                    <p class="mb-0">You have taken every available quiz.</p>
                @endforelse
                <a href="{{ route('quizzes.index') }}" class="d-inline-block mt-2">All quizzes</a>
            </div>
        </section>
    </div>
</div>

<section class="card" aria-labelledby="student-progress">
    <div class="card-body">
        <h2 id="student-progress" class="h5">My quiz progress</h2>
        @include('dashboard._progress', ['progress' => $progress])
    </div>
</section>

<section class="card" aria-labelledby="student-attempts">
    <div class="card-body">
        <h2 id="student-attempts" class="h5">My recent results</h2>
        @forelse ($attempts as $attempt)
            <p class="mb-1">
                <a href="{{ route('attempts.show', $attempt) }}">{{ $attempt->quiz->title }}</a>
                &middot; {{ $attempt->score }} / {{ $attempt->total }} ({{ $attempt->percentage() }}%)
                <span class="text-body-secondary small">&middot; {{ $attempt->created_at->format('M d, Y') }}</span>
            </p>
        @empty
            <p class="mb-0">You have not taken a quiz yet.</p>
        @endforelse
        <a href="{{ route('attempts.index') }}" class="d-inline-block mt-2">All my results</a>
    </div>
</section>
