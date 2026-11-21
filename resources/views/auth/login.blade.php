<x-guest-layout>
    <h1 class="h4 mb-1">Welcome back</h1>
    <p class="text-body-secondary mb-4">Log in to continue learning.</p>

    <x-auth-session-status :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="mb-3">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <div class="mb-3">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" />
        </div>

        <div class="form-check mb-4">
            <input id="remember_me" type="checkbox" class="form-check-input" name="remember">
            <label for="remember_me" class="form-check-label">{{ __('Remember me') }}</label>
        </div>

        <div class="d-grid mb-3">
            <x-primary-button class="btn-lg">{{ __('Log in') }}</x-primary-button>
        </div>

        <div class="d-flex justify-content-between small">
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}">{{ __('Forgot your password?') }}</a>
            @endif
            <a href="{{ route('register') }}">Create an account</a>
        </div>
    </form>
</x-guest-layout>
