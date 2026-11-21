@include('partials.form-errors')

<div class="row">
    <div class="col-md-4 mb-3">
        <label for="type" class="form-label">Type <span class="text-danger">*</span></label>
        <select id="type" name="type" class="form-select @error('type') is-invalid @enderror" required>
            <option value="">Select type</option>
            @foreach (\App\Models\Opportunity::TYPES as $type)
                <option value="{{ $type }}" @selected(old('type', $opportunity->type) === $type)>{{ ucfirst($type) }}</option>
            @endforeach
        </select>
        @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-8 mb-3">
        <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
        <input type="text" id="title" name="title" class="form-control @error('title') is-invalid @enderror"
               value="{{ old('title', $opportunity->title) }}" required>
        @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-3">
    <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
    <textarea id="description" name="description" rows="5"
              class="form-control @error('description') is-invalid @enderror" required>{{ old('description', $opportunity->description) }}</textarea>
    @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<div class="row">
    <div class="col-md-4 mb-3">
        <label for="location" class="form-label">Location</label>
        <input type="text" id="location" name="location" class="form-control @error('location') is-invalid @enderror"
               value="{{ old('location', $opportunity->location) }}">
        @error('location') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4 mb-3">
        <label for="event_date" class="form-label">Event date</label>
        <input type="date" id="event_date" name="event_date" class="form-control @error('event_date') is-invalid @enderror"
               value="{{ old('event_date', $opportunity->event_date?->format('Y-m-d')) }}">
        @error('event_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4 mb-3">
        <label for="deadline" class="form-label">Application deadline</label>
        <input type="date" id="deadline" name="deadline" class="form-control @error('deadline') is-invalid @enderror"
               value="{{ old('deadline', $opportunity->deadline?->format('Y-m-d')) }}">
        @error('deadline') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-3">
    <label for="link" class="form-label">More information link (https://...)</label>
    <input type="url" id="link" name="link" class="form-control @error('link') is-invalid @enderror"
           value="{{ old('link', $opportunity->link) }}">
    @error('link') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>
