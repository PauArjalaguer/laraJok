<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Has oblidat la contrasenya? Cap problema. Només cal que ens facilitis la teva adreça de correu electrònic i t’enviarem un enllaç per restablir la contrasenya, que et permetrà triar-ne una de nova.') }}
    </div>

    <!-- Estat de la sessió -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Adreça de correu electrònic -->
        <div>
            <x-input-label for="email" :value="__('Correu electrònic')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('Envia l’enllaç de restabliment de contrasenya') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>