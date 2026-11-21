@include('partials.form-errors')

<div class="mb-3">
    <label for="title" class="form-label">Title <span class="text-danger" aria-hidden="true">*</span></label>
    <input type="text" id="title" name="title" class="form-control @error('title') is-invalid @enderror"
           value="{{ old('title', $module->title) }}" required>
    @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<div class="row">
    <div class="col-md-4 mb-3">
        <label for="subject" class="form-label">Subject <span class="text-danger" aria-hidden="true">*</span></label>
        <input type="text" id="subject" name="subject" class="form-control @error('subject') is-invalid @enderror"
               value="{{ old('subject', $module->subject) }}" required>
        @error('subject') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4 mb-3">
        <label for="grade_level" class="form-label">Grade level <span class="text-danger" aria-hidden="true">*</span></label>
        <select id="grade_level" name="grade_level" class="form-select @error('grade_level') is-invalid @enderror" required>
            <option value="">Select grade level</option>
            @foreach (\App\Models\User::GRADE_LEVELS as $level)
                <option value="{{ $level }}" @selected(old('grade_level', $module->grade_level) === $level)>{{ $level }}</option>
            @endforeach
        </select>
        @error('grade_level') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4 mb-3">
        <label for="type" class="form-label">Type <span class="text-danger" aria-hidden="true">*</span></label>
        <select id="type" name="type" class="form-select @error('type') is-invalid @enderror" required>
            @foreach (\App\Models\Module::TYPES as $type)
                <option value="{{ $type }}" @selected(old('type', $module->type) === $type)>{{ ucfirst(str_replace('_', ' ', $type)) }}</option>
            @endforeach
        </select>
        @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-3">
    <label for="description" class="form-label">Description</label>
    <textarea id="description" name="description" rows="4"
              class="form-control @error('description') is-invalid @enderror">{{ old('description', $module->description) }}</textarea>
    @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<div class="mb-3">
    <label for="file" class="form-label">
        File (PDF, Word or PowerPoint, up to 20 MB)
        @if (! $module->exists) <span class="text-danger" aria-hidden="true">*</span> @endif
    </label>
    <input type="file" id="file" name="file" class="form-control @error('file') is-invalid @enderror"
           accept=".pdf,.doc,.docx,.ppt,.pptx" @required(! $module->exists)>
    @if ($module->exists)
        <div class="form-text">Current file: {{ $module->original_name }}. Choose a new file only to replace it.</div>
    @endif
    @error('file') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>
