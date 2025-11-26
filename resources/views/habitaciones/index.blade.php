<x-app-layout>
    <div class="container">
        <div class="page-header">
            <h1>Habitaciones</h1>
            @if(Auth::user()->role === 'gerente')
                <a href="{{ route('habitaciones.create') }}" class="btn btn-primary">Nueva Habitación</a>
            @endif
        </div>

        @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card">
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Número</th>
                            <th>Tipo</th>
                            <th>Precio por Noche</th>
                            <th>Estado</th>
                            <th>Piso</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($habitaciones as $habitacion)
                        <tr>
                            <td><strong>{{ $habitacion->numero }}</strong></td>
                            <td>{{ $habitacion->tipoHabitacion->nombre }}</td>
                            <td>${{ number_format($habitacion->precio_por_noche, 2) }}</td>
                            <td>
                                @if($habitacion->estado === 'disponible')
                                    <span style="color: #059669; font-weight: 600;">✓ Disponible</span>
                                @elseif($habitacion->estado === 'ocupada')
                                    <span style="color: #dc2626; font-weight: 600;">● Ocupada</span>
                                @elseif($habitacion->estado === 'limpieza')
                                    <span style="color: #f59e0b; font-weight: 600;">◐ Limpieza</span>
                                @else
                                    <span style="color: #6b7280; font-weight: 600;">⚠ Mantenimiento</span>
                                @endif
                            </td>
                            <td>{{ $habitacion->piso ?? 'N/A' }}</td>
                            <td>
                                @if(Auth::user()->role === 'gerente')
                                    <a href="{{ route('habitaciones.edit', $habitacion) }}" class="action-btn">Editar</a>
                                @else
                                    <span style="color: #9ca3af;">Ver</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">No hay habitaciones registradas</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="pagination-wrapper">
                    {{ $habitaciones->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
