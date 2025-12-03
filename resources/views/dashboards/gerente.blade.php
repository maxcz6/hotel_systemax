<x-app-layout>
    <x-slot name="header">
        <h2>{{ __('Dashboard - Gerente') }}</h2>
    </x-slot>

    <div class="dashboard-grid">
        <div class="dashboard-card">
            <h3>🏨 Habitaciones Disponibles</h3>
            <p class="big-number">{{ $habitacionesDisponibles }}</p>
        </div>
        <div class="dashboard-card">
            <h3>🔴 Habitaciones Ocupadas</h3>
            <p class="big-number">{{ $habitacionesOcupadas }}</p>
        </div>
        <div class="dashboard-card">
            <h3>🧹 Habitaciones en Limpieza</h3>
            <p class="big-number">{{ $habitacionesLimpieza }}</p>
        </div>
        <div class="dashboard-card">
            <h3>🔧 Habitaciones en Mantenimiento</h3>
            <p class="big-number">{{ $habitacionesMantenimiento }}</p>
        </div>
        <div class="dashboard-card">
            <h3>📅 Reservas del Día</h3>
            <p class="big-number">{{ $reservasHoy }}</p>
        </div>
        <div class="dashboard-card">
            <h3>✅ Check-in Pendientes</h3>
            <p class="big-number">{{ $checkinsPendientes }}</p>
        </div>
        <div class="dashboard-card">
            <h3>🚪 Check-out Pendientes</h3>
            <p class="big-number">{{ $checkoutsPendientes }}</p>
        </div>
        <div class="dashboard-card">
            <h3>💰 Ingresos del Día</h3>
            <p class="big-number">${{ number_format($ingresosHoy, 2) }}</p>
        </div>
        <div class="dashboard-card">
            <h3>📊 Ingresos del Mes</h3>
            <p class="big-number">${{ number_format($ingresosMes, 2) }}</p>
        </div>
    </div>

    <style>
        .big-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: #2563eb;
        }
    </style>
</x-app-layout>
