<section class="card mt-3" aria-labelledby="teacher-actions">
    <div class="card-body">
        <h2 id="teacher-actions" class="h5">What would you like to do?</h2>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('modules.create') }}" class="btn btn-primary">Upload module</a>
            <a href="{{ route('quizzes.create') }}" class="btn btn-primary">Create quiz</a>
            <a href="{{ route('grades.create') }}" class="btn btn-primary">Encode grade</a>
        </div>
        <p class="small mt-3 mb-0">
            Also:
            <a href="{{ route('announcements.create') }}">post an announcement</a> &middot;
            <a href="{{ route('schedules.create') }}">add a schedule</a>
        </p>
    </div>
</section>

<p class="text-body-secondary small mt-2">
    You have {{ $moduleCount }} {{ \Illuminate\Support\Str::plural('module', $moduleCount) }},
    {{ $quizCount }} {{ \Illuminate\Support\Str::plural('quiz', $quizCount) }} and
    {{ $gradeCount }} {{ \Illuminate\Support\Str::plural('grade', $gradeCount) }} encoded.
</p>
