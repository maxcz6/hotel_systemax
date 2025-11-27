<section>
    <header>
        <h2 style="font-size: 1.125rem; font-weight: 500; color: #111827;">
            Actualizar Contraseña
        </h2>

        <p style="margin-top: 0.25rem; font-size: 0.875rem; color: #4b5563;">
            Asegúrate de que tu cuenta esté usando una contraseña larga y aleatoria para mantenerla segura.
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-4">
        @csrf
        @method('put')

        <div class="form-group">
            <x-input-label for="update_password_current_password" value="Contraseña Actual" />
            <x-text-input id="update_password_current_password" name="current_password" type="password" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div class="form-group">
            <x-input-label for="update_password_password" value="Nueva Contraseña" />
            <x-text-input id="update_password_password" name="password" type="password" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div class="form-group">
            <x-input-label for="update_password_password_confirmation" value="Confirmar Contraseña" />
            <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>Guardar</x-primary-button>

            @if (session('status') === 'password-updated')
                <p id="password-status" class="text-sm text-gray-600">Guardado.</p>
                <script>
                    setTimeout(() => {
                        const status = document.getElementById('password-status');
                        if (status) {
                            status.style.display = 'none';
                        }
                    }, 2000);
                </script>
            @endif
        </div>
    </form>
</section>
