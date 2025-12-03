<section class="space-y-6">
    <header>
        <h2 style="font-size: 1.125rem; font-weight: 500; color: #111827;">
            Eliminar Cuenta
        </h2>

        <p style="margin-top: 0.25rem; font-size: 0.875rem; color: #4b5563;">
            Una vez que tu cuenta sea eliminada, todos sus recursos y datos serán eliminados permanentemente. Antes de eliminar tu cuenta, descarga cualquier dato o información que desees conservar.
        </p>
    </header>

    <button type="button" class="btn btn-danger" onclick="document.getElementById('confirm-user-deletion-modal').style.display='block'">
        Eliminar Cuenta
    </button>

    <div id="confirm-user-deletion-modal" class="modal" style="display: {{ $errors->userDeletion->isNotEmpty() ? 'block' : 'none' }};">
        <div class="modal-content">
            <form method="post" action="{{ route('profile.destroy') }}">
                @csrf
                @method('delete')

                <div class="modal-header">
                    <h2 style="font-size: 1.125rem; font-weight: 500; color: #111827;">
                        ¿Estás seguro de que deseas eliminar tu cuenta?
                    </h2>

                    <p style="margin-top: 0.25rem; font-size: 0.875rem; color: #4b5563;">
                        Una vez que tu cuenta sea eliminada, todos sus recursos y datos serán eliminados permanentemente. Por favor, ingresa tu contraseña para confirmar que deseas eliminar permanentemente tu cuenta.
                    </p>
                </div>

                <div class="form-group">
                    <x-input-label for="password" value="Contraseña" style="display: none;" />

                    <x-text-input
                        id="password"
                        name="password"
                        type="password"
                        placeholder="Contraseña"
                    />

                    <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="document.getElementById('confirm-user-deletion-modal').style.display='none'">
                        Cancelar
                    </button>

                    <button type="submit" class="btn btn-danger">
                        Eliminar Cuenta
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>
