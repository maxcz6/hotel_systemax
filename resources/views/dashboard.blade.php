<x-app-layout>
    <x-slot name="header">
        <h2>{{ __('Dashboard') }}</h2>
    </x-slot>

    <div class="dashboard-grid">
        <div class="dashboard-card">
            <h3>Habitaciones Disponibles</h3>
            <p>{{ $habitacionesDisponibles }}</p>
        </div>
        <div class="dashboard-card">
            <h3>Habitaciones Ocupadas</h3>
            <p>{{ $habitacionesOcupadas }}</p>
        </div>
        <div class="dashboard-card">
            <h3>Reservas del Día</h3>
            <p>{{ $reservasHoy }}</p>
        </div>
        <div class="dashboard-card">
            <h3>Check-in Pendientes</h3>
            <p>{{ $checkinsPendientes }}</p>
        </div>
        <div class="dashboard-card">
            <h3>Check-out Pendientes</h3>
            <p>{{ $checkoutsPendientes }}</p>
        </div>
        <div class="dashboard-card">
            <h3>Ingresos del Día</h3>
            <p>${{ number_format($ingresosHoy, 2) }}</p>
        </div>
    </div>
</x-app-layout>
