<x-app-layout>
    <div class="container">
        <div class="flex justify-between items-center mb-4">
            <h1>Habitaciones</h1>
            @if(in_array(Auth::user()->role, ['administrador', 'gerente']))
                <a href="{{ route('habitaciones.create') }}" class="btn btn-primary">+ Nueva Habitación</a>
            @endif
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="card">
            <div style="overflow-x: auto;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Tipo</th>
                            <th>Precio/Noche</th>
                            <th>Estado</th>
                            <th>Piso</th>
                            <th style="text-align: right;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($habitaciones as $habitacion)
                        <tr>
                            <td><strong>{{ $habitacion->numero }}</strong></td>
                            <td>{{ $habitacion->tipoHabitacion->nombre }}</td>
                            <td>${{ number_format($habitacion->precio_por_noche, 2) }}</td>
                            <td>
                                @php
                                    $estados = [
                                        'disponible' => ['color' => '#10b981', 'texto' => 'Disponible'],
                                        'ocupada' => ['color' => '#ef4444', 'texto' => 'Ocupada'],
                                        'limpieza' => ['color' => '#f59e0b', 'texto' => 'Limpieza'],
                                        'mantenimiento' => ['color' => '#6b7280', 'texto' => 'Mantenimiento']
                                    ];
                                    $estado = $estados[$habitacion->estado] ?? ['color' => '#000', 'texto' => $habitacion->estado];
                                @endphp
                                <span style="color: {{ $estado['color'] }}; font-weight: 600;">{{ $estado['texto'] }}</span>
                            </td>
                            <td>{{ $habitacion->piso ?? 'N/A' }}</td>
                            <td style="text-align: right;">
                                <a href="{{ route('habitaciones.show', $habitacion) }}" class="btn btn-sm btn-outline">Ver</a>
                                @if(in_array(Auth::user()->role, ['administrador', 'gerente']))
                                    <a href="{{ route('habitaciones.edit', $habitacion) }}" class="btn btn-sm btn-primary">Editar</a>
                                    <form action="{{ route('habitaciones.destroy', $habitacion) }}" method="POST" style="display:inline;" onsubmit="return confirm('¿Está seguro?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">No hay habitaciones registradas</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">
            {{ $habitaciones->links() }}
        </div>
    </div>
</x-app-layout>
