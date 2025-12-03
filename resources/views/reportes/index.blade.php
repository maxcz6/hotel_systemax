<x-app-layout>
    <div class="container">
        <div class="page-header">
            <h1>Reportes</h1>
        </div>

        <div class="dashboard-grid">
            <div class="card">
                <div class="card-body">
                    <h3>Reporte General</h3>
                    <p>Vista completa de reservas y estadísticas</p>
                    <a href="{{ route('reportes.general') }}" class="btn btn-primary">Ver Reporte</a>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h3>Ingresos</h3>
                    <p>Análisis de ingresos por período</p>
                    <a href="{{ route('reportes.ingresos') }}" class="btn btn-primary">Ver Reporte</a>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h3>Ocupación</h3>
                    <p>Estadísticas de ocupación de habitaciones</p>
                    <a href="{{ route('reportes.ocupacion') }}" class="btn btn-primary">Ver Reporte</a>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h3>Servicios</h3>
                    <p>Servicios más utilizados e ingresos</p>
                    <a href="{{ route('reportes.servicios') }}" class="btn btn-primary">Ver Reporte</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
