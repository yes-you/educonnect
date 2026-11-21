@include('partials.form-errors')

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="student_id" class="form-label">Student <span class="text-danger" aria-hidden="true">*</span></label>
        <select id="student_id" name="student_id" class="form-select @error('student_id') is-invalid @enderror" required>
            <option value="">Select student</option>
            @foreach ($students as $student)
                <option value="{{ $student->id }}" @selected((string) old('student_id', $grade->student_id) === (string) $student->id)>
                    {{ $student->name }} ({{ $student->grade_level }})
                </option>
            @endforeach
        </select>
        @error('student_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="subject" class="form-label">Subject <span class="text-danger" aria-hidden="true">*</span></label>
        <input type="text" id="subject" name="subject" class="form-control @error('subject') is-invalid @enderror"
               value="{{ old('subject', $grade->subject) }}" required>
        @error('subject') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="quarter" class="form-label">Quarter <span class="text-danger" aria-hidden="true">*</span></label>
        <select id="quarter" name="quarter" class="form-select @error('quarter') is-invalid @enderror" required>
            <option value="">Select quarter</option>
            @foreach (\App\Models\Grade::QUARTERS as $quarter)
                <option value="{{ $quarter }}" @selected((string) old('quarter', $grade->quarter) === (string) $quarter)>Quarter {{ $quarter }}</option>
            @endforeach
        </select>
        @error('quarter') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="grade" class="form-label">Grade (60 to 100) <span class="text-danger" aria-hidden="true">*</span></label>
        <input type="number" id="grade" name="grade" step="0.01" min="60" max="100"
               class="form-control @error('grade') is-invalid @enderror"
               value="{{ old('grade', $grade->grade) }}" required>
        @error('grade') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-3">
    <label for="remarks" class="form-label">Remarks</label>
    <input type="text" id="remarks" name="remarks" class="form-control @error('remarks') is-invalid @enderror"
           value="{{ old('remarks', $grade->remarks) }}">
    @error('remarks') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>
