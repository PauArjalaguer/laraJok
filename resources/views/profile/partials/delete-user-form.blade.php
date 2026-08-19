<section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Eliminar compte') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Una vegada eliminat el compte, tots els seus recursos i dades s\'esborraran permanentment. Abans d\'eliminar el teu compte, descarrega qualsevol dada o informació que vulguis conservar.') }}
        </p>
    </header>

    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
    >{{ __('Eliminar compte') }}</x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Segur que vols eliminar el teu compte?') }}
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                {{ __('Una vegada eliminat el compte, totes les seves dades s\'esborraran per sempre. Introdueix la teva contrasenya per confirmar que vols eliminar el compte positivament.') }}
            </p>

            <div class="mt-6">
                <x-input-label for="password" value="{{ __('Contrasenya') }}" class="sr-only" />

                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="mt-1 block w-3/4"
                    placeholder="{{ __('Contrasenya') }}"
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancel·lar') }}
                </x-secondary-button>

                <x-danger-button class="ms-3">
                    {{ __('Eliminar compte') }}
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
