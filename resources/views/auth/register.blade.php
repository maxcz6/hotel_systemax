<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Nombre -->
        <div class="form-group">
            <x-input-label for="name" value="Nombre" />
            <x-text-input id="name" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Correo Electrónico -->
        <div class="form-group">
            <x-input-label for="email" value="Correo Electrónico" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Contraseña -->
        <div class="form-group">
            <x-input-label for="password" value="Contraseña" />

            <x-text-input id="password"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirmar Contraseña -->
        <div class="form-group">
            <x-input-label for="password_confirmation" value="Confirmar Contraseña" />

            <x-text-input id="password_confirmation"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center flex-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('login') }}" style="margin-right: 1rem;">
                ¿Ya estás registrado?
            </a>

            <x-primary-button>
                Registrarse
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
