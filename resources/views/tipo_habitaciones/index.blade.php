<x-app-layout>
    <div class="container">
        <div class="page-header">
            <h1>Tipos de Habitación</h1>
            <a href="{{ route('tipo_habitaciones.create') }}" class="btn btn-primary">Nuevo Tipo de Habitación</a>
        </div>

        @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        <div class="card">
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Capacidad</th>
                            <th>Precio/Noche</th>
                            <th>N° Habitaciones</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tipoHabitaciones as $tipo)
                        <tr>
                            <td><strong>{{ $tipo->nombre }}</strong></td>
                            <td>{{ $tipo->descripcion ?? 'Sin descripción' }}</td>
                            <td>{{ $tipo->capacidad ?? 'N/A' }} personas</td>
                            <td>${{ number_format($tipo->precio_por_noche, 2) }}</td>
                            <td>{{ $tipo->habitaciones_count ?? $tipo->habitaciones->count() }}</td>
                            <td>
                                <a href="{{ route('tipo_habitaciones.edit', $tipo->id) }}" class="action-btn">Editar</a>
                                <form action="{{ route('tipo_habitaciones.destroy', $tipo->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('¿Está seguro de eliminar este tipo de habitación? Esto puede afectar las habitaciones existentes.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="delete-btn">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">No hay tipos de habitación registrados</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="pagination-wrapper">
                    {{ $tipoHabitaciones->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
