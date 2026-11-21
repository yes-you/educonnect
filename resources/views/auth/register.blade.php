<x-guest-layout>
    <h1 class="h4 mb-1">Create your account</h1>
    <p class="text-body-secondary mb-4">Join your school community on EduConnect.</p>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="mb-3">
            <x-input-label for="name" id="name-label" :value="__('Name')" />
            <x-text-input id="name" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" />
        </div>

        <div class="mb-3">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <div class="row">
            <div class="col-sm-6 mb-3">
                <x-input-label for="role" :value="__('I am a')" />
                <select id="role" name="role" required class="form-select" aria-describedby="role-hint">
                    <option value="student" @selected(old('role', 'student') === 'student')>Student</option>
                    <option value="parent" @selected(old('role') === 'parent')>Parent</option>
                    <option value="organization" @selected(old('role') === 'organization')>Organization</option>
                </select>
                <x-input-error :messages="$errors->get('role')" />
            </div>

            <div class="col-sm-6 mb-3" id="grade-field">
                <x-input-label for="grade_level" :value="__('Grade level')" />
                <select id="grade_level" name="grade_level" class="form-select">
                    <option value="">Select grade level</option>
                    @foreach (\App\Models\User::GRADE_LEVELS as $level)
                        <option value="{{ $level }}" @selected(old('grade_level') === $level)>{{ $level }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('grade_level')" />
            </div>
        </div>

        {{-- Tells each role what happens after registering; the text is swapped by the script below. --}}
        <p id="role-hint" class="small text-body-secondary mb-3" aria-live="polite"></p>

        <div class="mb-3">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" />
        </div>

        <div class="mb-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" />
        </div>

        <div class="d-grid mb-3">
            <x-primary-button class="btn-lg">{{ __('Register') }}</x-primary-button>
        </div>

        <p class="small text-center mb-0">
            <a href="{{ route('login') }}">{{ __('Already registered?') }}</a>
        </p>
    </form>

    <script>
        // What each role sees after registering, and what its name field means.
        const roles = {
            student: {
                name: 'Name',
                hint: 'Pick your grade to see the right modules and quizzes.',
            },
            parent: {
                name: 'Name',
                hint: 'After you register, the school administrator links your account to your child. Their grades, schedule and quiz results then appear on your dashboard.',
            },
            organization: {
                name: 'Organization name',
                hint: 'Postings you submit are shown to students only after the school administrator approves them.',
            },
        };

        const roleSelect = document.getElementById('role');
        const gradeField = document.getElementById('grade-field');
        const gradeSelect = document.getElementById('grade_level');

        function updateRole() {
            const role = roles[roleSelect.value];
            const isStudent = roleSelect.value === 'student';

            document.getElementById('name-label').textContent = role.name;
            document.getElementById('role-hint').textContent = role.hint;

            // Grade level only applies to students.
            gradeField.hidden = !isStudent;
            gradeSelect.disabled = !isStudent;
            if (!isStudent) {
                gradeSelect.value = '';
            }
        }

        roleSelect.addEventListener('change', updateRole);
        updateRole();
    </script>
</x-guest-layout>
