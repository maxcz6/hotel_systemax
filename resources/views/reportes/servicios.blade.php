<x-app-layout>
    <div class="container">
        <div class="page-header">
            <h1>Reporte de Servicios</h1>
            <a href="{{ route('reportes.index') }}" class="btn btn-secondary">Volver</a>
        </div>

        <form method="GET" action="{{ route('reportes.servicios') }}" class="card">
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

        <div class="card">
            <div class="card-body">
                <h3>Total Ingresos por Servicios: ${{ number_format($totalIngresosServicios, 2) }}</h3>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h3>Servicios Más Utilizados</h3>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Servicio</th>
                            <th>Cantidad Total</th>
                            <th>Ingresos Totales</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($serviciosMasUsados as $item)
                        <tr>
                            <td>{{ $item->servicio->nombre }}</td>
                            <td>{{ $item->total_cantidad }}</td>
                            <td>${{ number_format($item->total_ingresos, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center">No hay datos para el período seleccionado</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
