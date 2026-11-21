@include('partials.form-errors')

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
        <input type="text" id="title" name="title" class="form-control @error('title') is-invalid @enderror"
               value="{{ old('title', $schedule->title) }}" required>
        @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="subject" class="form-label">Subject <span class="text-danger">*</span></label>
        <input type="text" id="subject" name="subject" class="form-control @error('subject') is-invalid @enderror"
               value="{{ old('subject', $schedule->subject) }}" required>
        @error('subject') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="row">
    <div class="col-md-4 mb-3">
        <label for="grade_level" class="form-label">Grade level <span class="text-danger">*</span></label>
        <select id="grade_level" name="grade_level" class="form-select @error('grade_level') is-invalid @enderror" required>
            <option value="">Select grade level</option>
            @foreach (\App\Models\User::GRADE_LEVELS as $level)
                <option value="{{ $level }}" @selected(old('grade_level', $schedule->grade_level) === $level)>{{ $level }}</option>
            @endforeach
        </select>
        @error('grade_level') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4 mb-3">
        <label for="day" class="form-label">Day <span class="text-danger">*</span></label>
        <select id="day" name="day" class="form-select @error('day') is-invalid @enderror" required>
            <option value="">Select day</option>
            @foreach (\App\Models\Schedule::DAYS as $day)
                <option value="{{ $day }}" @selected(old('day', $schedule->day) === $day)>{{ $day }}</option>
            @endforeach
        </select>
        @error('day') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4 mb-3">
        <label for="room" class="form-label">Room</label>
        <input type="text" id="room" name="room" class="form-control @error('room') is-invalid @enderror"
               value="{{ old('room', $schedule->room) }}">
        @error('room') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="start_time" class="form-label">Start time <span class="text-danger">*</span></label>
        <input type="time" id="start_time" name="start_time" class="form-control @error('start_time') is-invalid @enderror"
               value="{{ old('start_time', $schedule->start_time?->format('H:i')) }}" required>
        @error('start_time') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="end_time" class="form-label">End time <span class="text-danger">*</span></label>
        <input type="time" id="end_time" name="end_time" class="form-control @error('end_time') is-invalid @enderror"
               value="{{ old('end_time', $schedule->end_time?->format('H:i')) }}" required>
        @error('end_time') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>
