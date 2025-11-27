<x-guest-layout>
    <!-- Estado de Sesión -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Correo Electrónico -->
        <div class="form-group">
            <x-input-label for="email" value="Correo Electrónico" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Contraseña -->
        <div class="form-group">
            <x-input-label for="password" value="Contraseña" />

            <x-text-input id="password"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Recuérdame -->
        <div class="form-check mb-4">
            <label for="remember_me" class="form-check">
                <input id="remember_me" type="checkbox" class="form-check-input" name="remember">
                <span class="form-check-label">Recuérdame</span>
            </label>
        </div>

        <div class="flex items-center flex-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('password.request') }}" style="margin-right: 1rem;">
                    ¿Olvidaste tu contraseña?
                </a>
            @endif

            <x-primary-button>
                Iniciar Sesión
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
