<x-app-layout>
    <div class="container">
        <div class="page-header">
            <h1>Reporte de Ocupación</h1>
            <a href="{{ route('reportes.index') }}" class="btn btn-secondary">Volver</a>
        </div>

        <form method="GET" action="{{ route('reportes.ocupacion') }}" class="card">
            <div class="card-body">
                <div class="form-row">
                    <div class="form-group">
                        <label for="fecha_inicio" class="form-label">Fecha Inicio</label>
                        <input type="date" name="fecha_inicio" id="fecha_inicio" value="{{ request('fecha_inicio', $fechaInicio instanceof \Carbon\Carbon ? $fechaInicio->format('Y-m-d') : $fechaInicio) }}" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="fecha_fin" class="form-label">Fecha Fin</label>
                        <input type="date" name="fecha_fin" id="fecha_fin" value="{{ request('fecha_fin', $fechaFin instanceof \Carbon\Carbon ? $fechaFin->format('Y-m-d') : $fechaFin) }}" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <button type="submit" class="btn btn-primary">Filtrar</button>
                    </div>
                </div>
            </div>
        </form>

        <div class="dashboard-grid">
            <div class="card">
                <div class="card-body">
                    <h3>Porcentaje de Ocupación</h3>
                    <p class="stat-number">{{ number_format($porcentajeOcupacion, 2) }}%</p>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h3>Total Habitaciones</h3>
                    <p class="stat-number">{{ $totalHabitaciones }}</p>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h3>Ocupación Diaria</h3>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Habitaciones Ocupadas</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ocupacionDiaria as $dia)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($dia->fecha)->format('d/m/Y') }}</td>
                            <td>{{ $dia->habitaciones_ocupadas }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="text-center">No hay datos para el período seleccionado</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h3>Habitaciones Más Ocupadas</h3>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Habitación</th>
                            <th>Tipo</th>
                            <th>Nº Reservas</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($habitacionesMasOcupadas as $habitacion)
                        <tr>
                            <td>{{ $habitacion->numero }}</td>
                            <td>{{ $habitacion->tipoHabitacion->nombre }}</td>
                            <td>{{ $habitacion->reservas_count }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center">No hay datos disponibles</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
