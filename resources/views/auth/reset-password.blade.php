<x-guest-layout>
    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <!-- Testimoni de restabliment de contrasenya -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Adreça de correu electrònic -->
        <div>
            <x-input-label for="email" :value="__('Correu electrònic')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Contrasenya -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Contrasenya')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirmar contrasenya -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirma la contrasenya')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                                type="password"
                                name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('Restableix la contrasenya') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>