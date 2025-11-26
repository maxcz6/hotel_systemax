<x-app-layout">
    <div class="container">
        <div class="page-header">
            <h1>Reporte General</h1>
            <a href="{{ route('reportes.index') }}" class="btn btn-secondary">Volver</a>
        </div>

        <form method="GET" action="{{ route('reportes.general') }}" class="card">
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
                    <h3>Total Reservas</h3>
                    <p class="stat-number">{{ $totalReservas }}</p>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h3>Total Ingresos</h3>
                    <p class="stat-number">${{ number_format($totalIngresos, 2) }}</p>
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
                <h3>Reservas por Estado</h3>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Estado</th>
                            <th>Cantidad</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reservasPorEstado as $item)
                        <tr>
                            <td>{{ ucfirst($item->estado) }}</td>
                            <td>{{ $item->total }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
