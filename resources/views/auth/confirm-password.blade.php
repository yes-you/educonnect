<x-guest-layout>
    <h1 class="h4 mb-1">Confirm your password</h1>
    <p class="text-body-secondary mb-4">
        {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
    </p>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <div class="mb-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" />
        </div>

        <div class="d-grid">
            <x-primary-button>{{ __('Confirm') }}</x-primary-button>
        </div>
    </form>
</x-guest-layout>
