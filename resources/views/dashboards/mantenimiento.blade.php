<x-app-layout>
    <x-slot name="header">
        <h2>{{ __('Dashboard - Mantenimiento') }}</h2>
    </x-slot>

    <div class="dashboard-grid">
        <div class="dashboard-card" style="background: #fef3c7;">
            <h3>Habitaciones en Mantenimiento</h3>
            <p class="big-number" style="color: #f59e0b;">{{ $habitacionesMantenimiento }}</p>
        </div>
        <div class="dashboard-card" style="background: #d1fae5;">
            <h3>Habitaciones Disponibles</h3>
            <p class="big-number" style="color: #10b981;">{{ $habitacionesDisponibles }}</p>
        </div>
        <div class="dashboard-card" style="background: #fee2e2;">
            <h3>Habitaciones Ocupadas</h3>
            <p class="big-number" style="color: #ef4444;">{{ $habitacionesOcupadas }}</p>
        </div>
        <div class="dashboard-card">
            <h3>Total Habitaciones</h3>
            <p class="big-number">{{ $totalHabitaciones }}</p>
        </div>
    </div>

    <div style="margin-top: 2rem;">
        <h3>Acciones</h3>
        <div style="margin-top: 1rem;">
            <a href="{{ route('mantenimiento.habitaciones') }}" class="btn btn-primary">Ver Todas las Habitaciones</a>
        </div>
    </div>

    <style>
        .big-number {
            font-size: 2.5rem;
            font-weight: bold;
        }
    </style>
</x-app-layout>
