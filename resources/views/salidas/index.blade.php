<x-app-layout>
    <div class="container">
        <div class="flex justify-between items-center mb-4">
            <h1>Salidas de Clientes</h1>
            @if(in_array(Auth::user()->role, ['administrador', 'gerente', 'recepcion']))
                <a href="{{ route('salidas.create') }}" class="btn btn-primary">+ Registrar Salida</a>
            @endif
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="card">
            <div class="card-header">Historial de Salidas</div>
            <div style="overflow-x: auto;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Reserva</th>
                            <th>Cliente</th>
                            <th>Habitación</th>
                            <th>Fecha Salida</th>
                            <th>Monto</th>
                            <th>Estado</th>
                            <th style="text-align: right;">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($salidas as $salida)
                        <tr>
                            <td><a href="{{ route('salidas.show', $salida) }}" class="badge badge-primary">#{{ $salida->id }}</a></td>
                            <td><a href="{{ route('reservas.show', $salida->reserva) }}">#{{ $salida->reserva_id }}</a></td>
                            <td><strong>{{ $salida->cliente->nombre }} {{ $salida->cliente->apellido }}</strong></td>
                            <td><span class="badge badge-info">#{{ $salida->habitacion->numero }}</span></td>
                            <td>{{ $salida->fecha_salida_real->format('d/m H:i') }}</td>
                            <td><strong>${{ number_format($salida->monto_total, 2) }}</strong></td>
                            <td>
                                @if($salida->estado === 'completado')
                                    <span class="badge badge-success">Completado</span>
                                @else
                                    <span class="badge badge-warning">Pendiente</span>
                                @endif
                            </td>
                            <td style="text-align: right;">
                                <a href="{{ route('salidas.show', $salida) }}" class="btn btn-sm btn-outline">Ver</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">No hay salidas registradas</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">
            {{ $salidas->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
