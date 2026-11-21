@extends('layouts.main')

@section('title', 'Parent links')

@section('content')
    <h1 class="h3 mb-3">Parent and student links</h1>
    <p class="text-body-secondary">A parent can see a student's grades, quiz results and schedule only after you link them here.</p>

    <section class="card" aria-labelledby="link-form-heading">
        <div class="card-body">
            <h2 id="link-form-heading" class="h5">Link a parent to a student</h2>

            <form method="POST" action="{{ route('admin.links.store') }}" class="row g-2 align-items-end">
                @csrf

                <div class="col-md-5">
                    <label for="parent_id" class="form-label">Parent <span class="text-danger" aria-hidden="true">*</span></label>
                    <select id="parent_id" name="parent_id" class="form-select @error('parent_id') is-invalid @enderror" required>
                        <option value="">Select parent</option>
                        @foreach ($parents as $parent)
                            <option value="{{ $parent->id }}" @selected((string) old('parent_id') === (string) $parent->id)>
                                {{ $parent->name }} ({{ $parent->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('parent_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-5">
                    <label for="student_id" class="form-label">Student <span class="text-danger" aria-hidden="true">*</span></label>
                    <select id="student_id" name="student_id" class="form-select @error('student_id') is-invalid @enderror" required>
                        <option value="">Select student</option>
                        @foreach ($students as $student)
                            <option value="{{ $student->id }}" @selected((string) old('student_id') === (string) $student->id)>
                                {{ $student->name }} ({{ $student->grade_level }})
                            </option>
                        @endforeach
                    </select>
                    @error('student_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-2 d-grid">
                    <button type="submit" class="btn btn-primary">Link</button>
                </div>
            </form>
        </div>
    </section>

    <h2 class="h4 mt-4">Current links</h2>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th scope="col">Parent</th>
                    <th scope="col">Student</th>
                    <th scope="col">Grade level</th>
                    <th scope="col"><span class="visually-hidden">Actions</span></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($links as $link)
                    <tr>
                        <td>{{ $link->parent_name }} <span class="text-body-secondary small">({{ $link->parent_email }})</span></td>
                        <td>{{ $link->student_name }}</td>
                        <td>{{ $link->student_grade }}</td>
                        <td>
                            <form method="POST" action="{{ route('admin.links.destroy', $link->id) }}"
                                  onsubmit="return confirm('Remove this link? The parent will no longer see this student.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Remove</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center py-4">No parents are linked to students yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $links->links() }}
@endsection
