<section class="space-y-6">
    <header>
        <h2 style="font-size: 1.125rem; font-weight: 500; color: #111827;">
            {{ __('Delete Account') }}
        </h2>

        <p style="margin-top: 0.25rem; font-size: 0.875rem; color: #4b5563;">
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
        </p>
    </header>

    <button type="button" class="btn btn-danger" onclick="document.getElementById('confirm-user-deletion-modal').style.display='block'">
        {{ __('Delete Account') }}
    </button>

    <div id="confirm-user-deletion-modal" class="modal" style="display: {{ $errors->userDeletion->isNotEmpty() ? 'block' : 'none' }};">
        <div class="modal-content">
            <form method="post" action="{{ route('profile.destroy') }}">
                @csrf
                @method('delete')

                <div class="modal-header">
                    <h2 style="font-size: 1.125rem; font-weight: 500; color: #111827;">
                        {{ __('Are you sure you want to delete your account?') }}
                    </h2>

                    <p style="margin-top: 0.25rem; font-size: 0.875rem; color: #4b5563;">
                        {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
                    </p>
                </div>

                <div class="form-group">
                    <x-input-label for="password" value="{{ __('Password') }}" style="display: none;" />

                    <x-text-input
                        id="password"
                        name="password"
                        type="password"
                        placeholder="{{ __('Password') }}"
                    />

                    <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="document.getElementById('confirm-user-deletion-modal').style.display='none'">
                        {{ __('Cancel') }}
                    </button>

                    <button type="submit" class="btn btn-danger">
                        {{ __('Delete Account') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>
