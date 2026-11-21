<div class="row mt-3">
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <p class="display-6 mb-0">{{ $moduleCount }}</p>
                <p class="mb-0">My modules</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <p class="display-6 mb-0">{{ $quizCount }}</p>
                <p class="mb-0">My quizzes</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <p class="display-6 mb-0">{{ $gradeCount }}</p>
                <p class="mb-0">Grades encoded</p>
            </div>
        </div>
    </div>
</div>

<section class="card" aria-labelledby="teacher-actions">
    <div class="card-body">
        <h2 id="teacher-actions" class="h5">Quick actions</h2>
        <a href="{{ route('modules.create') }}" class="btn btn-primary mb-1">Upload module</a>
        <a href="{{ route('quizzes.create') }}" class="btn btn-primary mb-1">Create quiz</a>
        <a href="{{ route('grades.create') }}" class="btn btn-primary mb-1">Encode grade</a>
        <a href="{{ route('announcements.create') }}" class="btn btn-outline-primary mb-1">Post announcement</a>
        <a href="{{ route('schedules.create') }}" class="btn btn-outline-primary mb-1">Add schedule</a>
    </div>
</section>
