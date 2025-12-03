<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center mb-4">
            <h1>Panel de Control</h1>
            <span class="text-muted text-sm">{{ Carbon\Carbon::now()->format('d/m/Y - H:i') }}</span>
        </div>
    </x-slot>

    <div class="container">
        <!-- Métricas Clave -->
        <div class="row cols-2 mb-4">
            <!-- Habitaciones Disponibles -->
            <div class="card card-primary">
                <div class="card-header">Habitaciones</div>
                <div class="card-body">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-muted mb-1">Disponibles</p>
                            <h2 style="margin: 0;">{{ $habitacionesDisponibles }}/{{ $habitacionesTotales }}</h2>
                            <small class="text-success">Listas para usar</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Habitaciones Ocupadas -->
            <div class="card card-danger">
                <div class="card-header">Ocupación</div>
                <div class="card-body">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-muted mb-1">Ocupadas</p>
                            <h2 style="margin: 0;">{{ $habitacionesOcupadas }}</h2>
                            <small>Tasa: <strong>{{ $tasaOcupacion }}%</strong></small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ingresos del Día -->
            <div class="card card-success">
                <div class="card-header">Ingresos Hoy</div>
                <div class="card-body">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-muted mb-1">Diario</p>
                            <h2 style="margin: 0;">${{ number_format($ingresosHoy, 2) }}</h2>
                            <small class="text-success">Mes: ${{ number_format($ingresosMes, 2) }}</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pagos Pendientes -->
            <div class="card">
                <div class="card-header">Pagos Pendientes</div>
                <div class="card-body">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-muted mb-1">Sin completar</p>
                            <h2 style="margin: 0; color: #ef4444;">{{ $pagosPendientes }}</h2>
                            <small class="text-danger">Reservas pendientes</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Operaciones del Día -->
        <div class="row cols-3 mb-4">
            <!-- Check-in Pendientes -->
            <div class="card">
                <div class="card-header">Check-in Pendientes</div>
                <div class="card-body text-center">
                    <h2 style="margin: 0; color: #3b82f6;">{{ $checkinsPendientes }}</h2>
                    <p class="text-muted mt-2 mb-0">Total de {{ $reservasHoy }} reservas</p>
                </div>
            </div>

            <!-- Check-out Pendientes -->
            <div class="card">
                <div class="card-header">Check-out Pendientes</div>
                <div class="card-body text-center">
                    <h2 style="margin: 0; color: #f59e0b;">{{ $checkoutsPendientes }}</h2>
                    <p class="text-muted mt-2 mb-0">Habitaciones a liberar</p>
                </div>
            </div>

            <!-- Clientes Activos -->
            <div class="card">
                <div class="card-header">Clientes Activos</div>
                <div class="card-body text-center">
                    <h2 style="margin: 0; color: #0ea5e9;">{{ $clientesActivos }}</h2>
                    <p class="text-muted mt-2 mb-0">Con reservas en progreso</p>
                </div>
            </div>
        </div>

        <!-- Tablas de Ocupación y Reservas -->
        <div class="row cols-2 mb-4">
            <!-- Ocupación por Tipo de Habitación -->
            <div class="card">
                <div class="card-header">Ocupación por Tipo</div>
                <div class="card-body">
                    <div style="overflow-x: auto;">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Tipo</th>
                                    <th>Total</th>
                                    <th>Ocupadas</th>
                                    <th>Disponibles</th>
                                    <th>%</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ocupacionPorTipo as $tipo)
                                <tr>
                                    <td><strong>{{ $tipo->nombre }}</strong></td>
                                    <td><span class="badge badge-info">{{ $tipo->habitaciones_count }}</span></td>
                                    <td><span class="badge badge-warning">{{ $tipo->ocupadas }}</span></td>
                                    <td><span class="badge badge-success">{{ $tipo->habitaciones_count - $tipo->ocupadas }}</span></td>
                                    <td>
                                        @php
                                            $ocupacion = $tipo->habitaciones_count > 0 ? round(($tipo->ocupadas / $tipo->habitaciones_count) * 100) : 0;
                                            $color = $ocupacion > 75 ? '#ef4444' : ($ocupacion > 50 ? '#f59e0b' : '#10b981');
                                        @endphp
                                        <div class="progress">
                                            <div class="progress-bar" style="width: {{ $ocupacion }}%; background-color: {{ $color }};"></div>
                                        </div>
                                        <small>{{ $ocupacion }}%</small>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Sin datos</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Próximas Reservas -->
            <div class="card">
                <div class="card-header">Próximas Reservas (7 días)</div>
                <div class="card-body">
                    <div style="overflow-x: auto;">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Cliente</th>
                                    <th>Habitación</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reservasProximas as $reserva)
                                <tr>
                                    <td><a href="{{ route('reservas.show', $reserva) }}" class="badge badge-primary">#{{ $reserva->id }}</a></td>
                                    <td>{{ $reserva->cliente->nombre }} {{ $reserva->cliente->apellido }}</td>
                                    <td><span class="badge badge-info">{{ $reserva->habitacion->numero }}</span></td>
                                    <td>{{ $reserva->fecha_entrada->format('d/m') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">Sin reservas próximas</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Accesos Rápidos -->
        <div class="card">
            <div class="card-header">Accesos Rápidos</div>
            <div class="card-body">
                <div class="row cols-3">
                    <a href="{{ route('reservas.index') }}" class="btn btn-primary btn-lg" style="width: 100%; text-align: center;">
                        Reservas
                    </a>
                    <a href="{{ route('pagos.index') }}" class="btn btn-success btn-lg" style="width: 100%; text-align: center;">
                        Pagos
                    </a>
                    <a href="{{ route('salidas.index') }}" class="btn btn-danger btn-lg" style="width: 100%; text-align: center;">
                        Salidas
                    </a>
                    <a href="{{ route('habitaciones.index') }}" class="btn btn-info btn-lg" style="width: 100%; text-align: center;">
                        Habitaciones
                    </a>
                    <a href="{{ route('mantenimientos.index') }}" class="btn btn-outline btn-lg" style="width: 100%; text-align: center;">
                        Mantenimiento
                    </a>
                    <a href="{{ route('clientes.index') }}" class="btn btn-warning btn-lg" style="width: 100%; text-align: center;">
                        Clientes
                    </a>
                </div>
            </div>
        </div>
    </div>

    <style>
        .card {
            animation: slideIn 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .progress {
            height: 0.5rem;
            margin-bottom: 0.5rem;
        }

        .table {
            font-size: 0.9rem;
        }

        @media (max-width: 768px) {
            .row.cols-3 {
                grid-template-columns: 1fr;
            }
        }
    </style>
</x-app-layout>
            <!-- Habitaciones Disponibles -->
            <div class="card card-primary">
                <div class="card-header">Habitaciones</div>
                <div class="card-body">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-muted mb-1">Disponibles</p>
                            <h2 style="margin: 0;">{{ $habitacionesDisponibles }}/{{ $habitacionesTotales }}</h2>
                            <small class="text-success">Listas para usar</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Habitaciones Ocupadas -->
            <div class="card card-danger">
                <div class="card-header">Ocupación</div>
                <div class="card-body">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-muted mb-1">Ocupadas</p>
                            <h2 style="margin: 0;">{{ $habitacionesOcupadas }}</h2>
                            <small>Tasa: <strong>{{ $tasaOcupacion }}%</strong></small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ingresos del Día -->
            <div class="card card-success">
                <div class="card-header">Ingresos Hoy</div>
                <div class="card-body">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-muted mb-1">Diario</p>
                            <h2 style="margin: 0;">${{ number_format($ingresosHoy, 2) }}</h2>
                            <small class="text-success">Mes: ${{ number_format($ingresosMes, 2) }}</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pagos Pendientes -->
            <div class="card">
                <div class="card-header">Pagos Pendientes</div>
                <div class="card-body">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-muted mb-1">Sin completar</p>
                            <h2 style="margin: 0; color: #ef4444;">{{ $pagosPendientes }}</h2>
                            <small class="text-danger">Reservas pendientes</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Operaciones del Día -->
        <div class="row cols-3 mb-4">
            <!-- Check-in Pendientes -->
            <div class="card">
                <div class="card-header">Check-in Pendientes</div>
                <div class="card-body text-center">
                    <h2 style="margin: 0; color: #3b82f6;">{{ $checkinsPendientes }}</h2>
                    <p class="text-muted mt-2 mb-0">Total de {{ $reservasHoy }} reservas</p>
                </div>
            </div>

            <!-- Check-out Pendientes -->
            <div class="card">
                <div class="card-header">Check-out Pendientes</div>
                <div class="card-body text-center">
                    <h2 style="margin: 0; color: #f59e0b;">{{ $checkoutsPendientes }}</h2>
                    <p class="text-muted mt-2 mb-0">Habitaciones a liberar</p>
                </div>
            </div>

            <!-- Clientes Activos -->
            <div class="card">
                <div class="card-header">Clientes Activos</div>
                <div class="card-body text-center">
                    <h2 style="margin: 0; color: #0ea5e9;">{{ $clientesActivos }}</h2>
                    <p class="text-muted mt-2 mb-0">Con reservas en progreso</p>
                </div>
            </div>
        </div>

        <!-- Tablas de Ocupación y Reservas -->
        <div class="row cols-2 mb-4">
            <!-- Ocupación por Tipo de Habitación -->
            <div class="card">
                <div class="card-header">Ocupación por Tipo</div>
                <div class="card-body">
                    <div style="overflow-x: auto;">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Tipo</th>
                                    <th>Total</th>
                                    <th>Ocupadas</th>
                                    <th>Disponibles</th>
                                    <th>%</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ocupacionPorTipo as $tipo)
                                <tr>
                                    <td><strong>{{ $tipo->nombre }}</strong></td>
                                    <td><span class="badge badge-info">{{ $tipo->habitaciones_count }}</span></td>
                                    <td><span class="badge badge-warning">{{ $tipo->ocupadas }}</span></td>
                                    <td><span class="badge badge-success">{{ $tipo->habitaciones_count - $tipo->ocupadas }}</span></td>
                                    <td>
                                        @php
                                            $ocupacion = $tipo->habitaciones_count > 0 ? round(($tipo->ocupadas / $tipo->habitaciones_count) * 100) : 0;
                                            $color = $ocupacion > 75 ? '#ef4444' : ($ocupacion > 50 ? '#f59e0b' : '#10b981');
                                        @endphp
                                        <div class="progress">
                                            <div class="progress-bar" style="width: {{ $ocupacion }}%; background-color: {{ $color }};"></div>
                                        </div>
                                        <small>{{ $ocupacion }}%</small>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Sin datos</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Próximas Reservas -->
            <div class="card">
                <div class="card-header">Próximas Reservas (7 días)</div>
                <div class="card-body">
                    <div style="overflow-x: auto;">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Cliente</th>
                                    <th>Habitación</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reservasProximas as $reserva)
                                <tr>
                                    <td><a href="{{ route('reservas.show', $reserva) }}" class="badge badge-primary">#{{ $reserva->id }}</a></td>
                                    <td>{{ $reserva->cliente->nombre }} {{ $reserva->cliente->apellido }}</td>
                                    <td><span class="badge badge-info">{{ $reserva->habitacion->numero }}</span></td>
                                    <td>{{ $reserva->fecha_entrada->format('d/m') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">Sin reservas próximas</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Accesos Rápidos -->
        <div class="card">
            <div class="card-header">Accesos Rápidos</div>
            <div class="card-body">
                <div class="row cols-3">
                    <a href="{{ route('reservas.index') }}" class="btn btn-primary btn-lg" style="width: 100%; text-align: center;">
                        Reservas
                    </a>
                    <a href="{{ route('pagos.index') }}" class="btn btn-success btn-lg" style="width: 100%; text-align: center;">
                        Pagos
                    </a>
                    <a href="{{ route('salidas.index') }}" class="btn btn-danger btn-lg" style="width: 100%; text-align: center;">
                        Salidas
                    </a>
                    <a href="{{ route('habitaciones.index') }}" class="btn btn-info btn-lg" style="width: 100%; text-align: center;">
                        Habitaciones
                    </a>
                    <a href="{{ route('mantenimientos.index') }}" class="btn btn-outline btn-lg" style="width: 100%; text-align: center;">
                        Mantenimiento
                    </a>
                    <a href="{{ route('clientes.index') }}" class="btn btn-warning btn-lg" style="width: 100%; text-align: center;">
                        Clientes
                    </a>
                </div>
            </div>
        </div>
    </div>

    <style>
        .card {
            animation: slideIn 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .progress {
            height: 0.5rem;
            margin-bottom: 0.5rem;
        }

        .table {
            font-size: 0.9rem;
        }

        @media (max-width: 768px) {
            .row.cols-3 {
                grid-template-columns: 1fr;
            }
        }
    </style>
</x-app-layout>
                <div class="card-header">Ocupación</div>
                <div class="card-body">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-muted mb-1">Ocupadas</p>
                            <h2 style="margin: 0;">{{ $habitacionesOcupadas }}</h2>
                            <small>Tasa: <strong>{{ $tasaOcupacion }}%</strong></small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ingresos del Día -->
            <div class="card card-success">
                <div class="card-header">Ingresos Hoy</div>
                <div class="card-body">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-muted mb-1">Diario</p>
                            <h2 style="margin: 0;">${{ number_format($ingresosHoy, 2) }}</h2>
                            <small class="text-success">Mes: ${{ number_format($ingresosMes, 2) }}</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pagos Pendientes -->
            <div class="card">
                <div class="card-header">Pagos Pendientes</div>
                <div class="card-body">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-muted mb-1">Sin completar</p>
                            <h2 style="margin: 0; color: #ef4444;">{{ $pagosPendientes }}</h2>
                            <small class="text-danger">Reservas pendientes</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Operaciones del Día -->
        <div class="row cols-3 mb-4">
            <!-- Check-in Pendientes -->
            <div class="card">
                <div class="card-header">Check-in Pendientes</div>
                <div class="card-body text-center">
                    <h2 style="margin: 0; color: #3b82f6;">{{ $checkinsPendientes }}</h2>
                    <p class="text-muted mt-2 mb-0">Total de {{ $reservasHoy }} reservas</p>
                </div>
            </div>

            <!-- Check-out Pendientes -->
            <div class="card">
                <div class="card-header">Check-out Pendientes</div>
                <div class="card-body text-center">
                    <h2 style="margin: 0; color: #f59e0b;">{{ $checkoutsPendientes }}</h2>
                    <p class="text-muted mt-2 mb-0">Habitaciones a liberar</p>
                </div>
            </div>

            <!-- Clientes Activos -->
            <div class="card">
                <div class="card-header">Clientes Activos</div>
                <div class="card-body text-center">
                    <h2 style="margin: 0; color: #0ea5e9;">{{ $clientesActivos }}</h2>
                    <p class="text-muted mt-2 mb-0">Con reservas en progreso</p>
                </div>
            </div>
        </div>

        <!-- Tablas de Ocupación y Reservas -->
        <div class="row cols-2 mb-4">
            <!-- Ocupación por Tipo de Habitación -->
            <div class="card">
                <div class="card-header">Ocupación por Tipo</div>
                <div class="card-body">
                    <div style="overflow-x: auto;">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Tipo</th>
                                    <th>Total</th>
                                    <th>Ocupadas</th>
                                    <th>Disponibles</th>
                                    <th>%</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ocupacionPorTipo as $tipo)
                                <tr>
                                    <td><strong>{{ $tipo->nombre }}</strong></td>
                                    <td><span class="badge badge-info">{{ $tipo->habitaciones_count }}</span></td>
                                    <td><span class="badge badge-warning">{{ $tipo->ocupadas }}</span></td>
                                    <td><span class="badge badge-success">{{ $tipo->habitaciones_count - $tipo->ocupadas }}</span></td>
                                    <td>
                                        @php
                                            $ocupacion = $tipo->habitaciones_count > 0 ? round(($tipo->ocupadas / $tipo->habitaciones_count) * 100) : 0;
                                            $color = $ocupacion > 75 ? '#ef4444' : ($ocupacion > 50 ? '#f59e0b' : '#10b981');
                                        @endphp
                                        <div class="progress">
                                            <div class="progress-bar" style="width: {{ $ocupacion }}%; background-color: {{ $color }};"></div>
                                        </div>
                                        <small>{{ $ocupacion }}%</small>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Sin datos</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Próximas Reservas -->
            <div class="card">
                <div class="card-header">Próximas Reservas (7 días)</div>
                <div class="card-body">
                    <div style="overflow-x: auto;">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Cliente</th>
                                    <th>Habitación</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reservasProximas as $reserva)
                                <tr>
                                    <td><a href="{{ route('reservas.show', $reserva) }}" class="badge badge-primary">#{{ $reserva->id }}</a></td>
                                    <td>{{ $reserva->cliente->nombre }} {{ $reserva->cliente->apellido }}</td>
                                    <td><span class="badge badge-info">{{ $reserva->habitacion->numero }}</span></td>
                                    <td>{{ $reserva->fecha_entrada->format('d/m') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">Sin reservas próximas</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Accesos Rápidos -->
        <div class="card">
            <div class="card-header">Accesos Rápidos</div>
            <div class="card-body">
                <div class="row cols-3">
                    <a href="{{ route('reservas.index') }}" class="btn btn-primary btn-lg" style="width: 100%; text-align: center;">
                        Reservas
                    </a>
                    <a href="{{ route('pagos.index') }}" class="btn btn-success btn-lg" style="width: 100%; text-align: center;">
                        Pagos
                    </a>
                    <a href="{{ route('salidas.index') }}" class="btn btn-danger btn-lg" style="width: 100%; text-align: center;">
                        Salidas
                    </a>
                    <a href="{{ route('habitaciones.index') }}" class="btn btn-info btn-lg" style="width: 100%; text-align: center;">
                        Habitaciones
                    </a>
                    <a href="{{ route('mantenimientos.index') }}" class="btn btn-outline btn-lg" style="width: 100%; text-align: center;">
                        Mantenimiento
                    </a>
                    <a href="{{ route('clientes.index') }}" class="btn btn-warning btn-lg" style="width: 100%; text-align: center;">
                        Clientes
                    </a>
                </div>
            </div>
        </div>
    </div>

    <style>
        .card {
            animation: slideIn 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .progress {
            height: 0.5rem;
            margin-bottom: 0.5rem;
        }

        .table {
            font-size: 0.9rem;
        }

        @media (max-width: 768px) {
            .row.cols-3 {
                grid-template-columns: 1fr;
            }
        }
    </style>
</x-app-layout>
                        <div style="font-size: 2.5rem; opacity: 0.3;">💵</div>
                    </div>
                </div>
            </div>

            <!-- Pagos Pendientes -->
            <div class="card">
                <div class="card-header">Pagos Pendientes</div>
                <div class="card-body">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-muted mb-1">Sin completar</p>
                            <h2 style="margin: 0; color: #ef4444;">{{ $pagosPendientes }}</h2>
                            <small class="text-danger">Reservas pendientes</small>
                        </div>
                        <div style="font-size: 2.5rem; opacity: 0.3;">⚠️</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Operaciones del Día -->
        <div class="row cols-3 mb-4">
            <!-- Check-in Pendientes -->
            <div class="card">
                <div class="card-header text-primary">✓ Check-in Pendientes</div>
                <div class="card-body text-center">
                    <h2 style="margin: 0; color: #3b82f6;">{{ $checkinsPendientes }}</h2>
                    <p class="text-muted mt-2 mb-0">Total de {{ $reservasHoy }} reservas</p>
                </div>
            </div>

            <!-- Check-out Pendientes -->
            <div class="card">
                <div class="card-header text-warning">✕ Check-out Pendientes</div>
                <div class="card-body text-center">
                    <h2 style="margin: 0; color: #f59e0b;">{{ $checkoutsPendientes }}</h2>
                    <p class="text-muted mt-2 mb-0">Habitaciones a liberar</p>
                </div>
            </div>

            <!-- Clientes Activos -->
            <div class="card">
                <div class="card-header text-info">👥 Clientes Activos</div>
                <div class="card-body text-center">
                    <h2 style="margin: 0; color: #0ea5e9;">{{ $clientesActivos }}</h2>
                    <p class="text-muted mt-2 mb-0">Con reservas en progreso</p>
                </div>
            </div>
        </div>

        <!-- Tablas de Ocupación y Reservas -->
        <div class="row cols-2 mb-4">
            <!-- Ocupación por Tipo de Habitación -->
            <div class="card">
                <div class="card-header">📈 Ocupación por Tipo</div>
                <div class="card-body">
                    <div style="overflow-x: auto;">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Tipo</th>
                                    <th>Total</th>
                                    <th>Ocupadas</th>
                                    <th>Disponibles</th>
                                    <th>%</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ocupacionPorTipo as $tipo)
                                <tr>
                                    <td><strong>{{ $tipo->nombre }}</strong></td>
                                    <td><span class="badge badge-info">{{ $tipo->habitaciones_count }}</span></td>
                                    <td><span class="badge badge-warning">{{ $tipo->ocupadas }}</span></td>
                                    <td><span class="badge badge-success">{{ $tipo->habitaciones_count - $tipo->ocupadas }}</span></td>
                                    <td>
                                        @php
                                            $ocupacion = $tipo->habitaciones_count > 0 ? round(($tipo->ocupadas / $tipo->habitaciones_count) * 100) : 0;
                                            $color = $ocupacion > 75 ? '#ef4444' : ($ocupacion > 50 ? '#f59e0b' : '#10b981');
                                        @endphp
                                        <div class="progress">
                                            <div class="progress-bar" style="width: {{ $ocupacion }}%; background-color: {{ $color }};"></div>
                                        </div>
                                        <small>{{ $ocupacion }}%</small>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Sin datos</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Próximas Reservas -->
            <div class="card">
                <div class="card-header">📅 Próximas Reservas (7 días)</div>
                <div class="card-body">
                    <div style="overflow-x: auto;">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Cliente</th>
                                    <th>Habitación</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reservasProximas as $reserva)
                                <tr>
                                    <td><a href="{{ route('reservas.show', $reserva) }}" class="badge badge-primary">#{{ $reserva->id }}</a></td>
                                    <td>{{ $reserva->cliente->nombre }} {{ $reserva->cliente->apellido }}</td>
                                    <td><span class="badge badge-info">{{ $reserva->habitacion->numero }}</span></td>
                                    <td>{{ $reserva->fecha_entrada->format('d/m') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">Sin reservas próximas</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Accesos Rápidos -->
        <div class="card">
            <div class="card-header">⚡ Accesos Rápidos</div>
            <div class="card-body">
                <div class="row cols-3">
                    <a href="{{ route('reservas.index') }}" class="btn btn-primary btn-lg" style="width: 100%; text-align: center;">
                        📅 Reservas
                    </a>
                    <a href="{{ route('pagos.index') }}" class="btn btn-success btn-lg" style="width: 100%; text-align: center;">
                        💳 Pagos
                    </a>
                    <a href="{{ route('salidas.index') }}" class="btn btn-danger btn-lg" style="width: 100%; text-align: center;">
                        🚪 Salidas
                    </a>
                    <a href="{{ route('habitaciones.index') }}" class="btn btn-info btn-lg" style="width: 100%; text-align: center;">
                        🏠 Habitaciones
                    </a>
                    <a href="{{ route('mantenimientos.index') }}" class="btn btn-outline btn-lg" style="width: 100%; text-align: center;">
                        🔧 Mantenimiento
                    </a>
                    <a href="{{ route('clientes.index') }}" class="btn btn-warning btn-lg" style="width: 100%; text-align: center;">
                        👥 Clientes
                    </a>
                </div>
            </div>
        </div>
    </div>

    <style>
        .card {
            animation: slideIn 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .progress {
            height: 0.5rem;
            margin-bottom: 0.5rem;
        }

        .table {
            font-size: 0.9rem;
        }

        @media (max-width: 768px) {
            .row.cols-3 {
                grid-template-columns: 1fr;
            }
        }
    </style>

