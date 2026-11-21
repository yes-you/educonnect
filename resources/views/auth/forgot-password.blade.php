<x-guest-layout>
    <h1 class="h4 mb-1">Forgot your password?</h1>
    <p class="text-body-secondary mb-4">
        {{ __('No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
    </p>

    <x-auth-session-status :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="mb-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <div class="d-grid mb-3">
            <x-primary-button>{{ __('Email Password Reset Link') }}</x-primary-button>
        </div>

        <p class="small text-center mb-0"><a href="{{ route('login') }}">Back to log in</a></p>
    </form>
</x-guest-layout>
