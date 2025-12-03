<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Perfil
        </h2>
    </x-slot>

    <div class="container" style="padding-top: 3rem; padding-bottom: 3rem;">
        <div class="card">
            <div style="max-width: 40rem;">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <div class="card">
            <div style="max-width: 40rem;">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <div class="card">
            <div style="max-width: 40rem;">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-app-layout>
