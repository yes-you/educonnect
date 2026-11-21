@include('partials.form-errors')

<div class="mb-3">
    <label for="title" class="form-label">Title <span class="text-danger" aria-hidden="true">*</span></label>
    <input type="text" id="title" name="title" class="form-control @error('title') is-invalid @enderror"
           value="{{ old('title', $quiz->title) }}" required>
    @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<div class="row">
    <div class="col-md-4 mb-3">
        <label for="subject" class="form-label">Subject <span class="text-danger" aria-hidden="true">*</span></label>
        <input type="text" id="subject" name="subject" class="form-control @error('subject') is-invalid @enderror"
               value="{{ old('subject', $quiz->subject) }}" required>
        @error('subject') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4 mb-3">
        <label for="grade_level" class="form-label">Grade level <span class="text-danger" aria-hidden="true">*</span></label>
        <select id="grade_level" name="grade_level" class="form-select @error('grade_level') is-invalid @enderror" required>
            <option value="">Select grade level</option>
            @foreach (\App\Models\User::GRADE_LEVELS as $level)
                <option value="{{ $level }}" @selected(old('grade_level', $quiz->grade_level) === $level)>{{ $level }}</option>
            @endforeach
        </select>
        @error('grade_level') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4 mb-3">
        <label for="module_id" class="form-label">Related module (optional)</label>
        <select id="module_id" name="module_id" class="form-select @error('module_id') is-invalid @enderror">
            <option value="">None</option>
            @foreach ($modules as $module)
                <option value="{{ $module->id }}" @selected((string) old('module_id', $quiz->module_id) === (string) $module->id)>{{ $module->title }}</option>
            @endforeach
        </select>
        @error('module_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-3">
    <label for="description" class="form-label">Description</label>
    <textarea id="description" name="description" rows="3"
              class="form-control @error('description') is-invalid @enderror">{{ old('description', $quiz->description) }}</textarea>
    @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>
