<x-guest-layout>
    <h1 class="h4 mb-1">Create your account</h1>
    <p class="text-body-secondary mb-4">Join students, teachers and parents on EduConnect.</p>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="mb-3">
            <x-input-label for="name" :value="__('Name')" />
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
                <select id="role" name="role" required class="form-select">
                    <option value="student" @selected(old('role', 'student') === 'student')>Student</option>
                    <option value="parent" @selected(old('role') === 'parent')>Parent</option>
                    <option value="organization" @selected(old('role') === 'organization')>Organization</option>
                </select>
                <x-input-error :messages="$errors->get('role')" />
            </div>

            <div class="col-sm-6 mb-3">
                <x-input-label for="grade_level" :value="__('Grade level (students only)')" />
                <select id="grade_level" name="grade_level" class="form-select">
                    <option value="">Select grade level</option>
                    @foreach (\App\Models\User::GRADE_LEVELS as $level)
                        <option value="{{ $level }}" @selected(old('grade_level') === $level)>{{ $level }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('grade_level')" />
            </div>
        </div>

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
        // Grade level only applies to students: grey it out for parent and organization.
        const roleSelect = document.getElementById('role');
        const gradeSelect = document.getElementById('grade_level');

        function updateGradeLevel() {
            const isStudent = roleSelect.value === 'student';
            gradeSelect.disabled = !isStudent;
            if (!isStudent) {
                gradeSelect.value = '';
            }
        }

        roleSelect.addEventListener('change', updateGradeLevel);
        updateGradeLevel();
    </script>
</x-guest-layout>
