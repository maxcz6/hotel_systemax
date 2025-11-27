<x-app-layout>
    <div class="container">
        <div class="flex justify-between items-center mb-4">
            <h1>Reservas</h1>
            <a href="{{ route('reservas.create') }}" class="btn btn-primary">+ Nueva Reserva</a>
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
                            <th>Cliente</th>
                            <th>Habitación</th>
                            <th>Entrada</th>
                            <th>Salida</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th style="text-align: right;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reservas as $reserva)
                        <tr>
                            <td><span class="badge badge-primary">#{{ $reserva->id }}</span></td>
                            <td><strong>{{ $reserva->cliente->nombre }} {{ $reserva->cliente->apellido }}</strong></td>
                            <td>{{ $reserva->habitacion->numero }}</td>
                            <td>{{ $reserva->fecha_entrada->format('d/m') }}</td>
                            <td>{{ $reserva->fecha_salida->format('d/m') }}</td>
                            <td><strong>${{ number_format($reserva->total_precio, 2) }}</strong></td>
                            <td>
                                @php
                                    $estados = [
                                        'activa' => 'badge-primary',
                                        'confirmada' => 'badge-success',
                                        'cancelada' => 'badge-danger',
                                        'checkout' => 'badge-warning'
                                    ];
                                    $clase = $estados[$reserva->estado] ?? 'badge-info';
                                @endphp
                                <span class="badge {{ $clase }}">{{ ucfirst($reserva->estado) }}</span>
                            </td>
                            <td style="text-align: right;">
                                <a href="{{ route('reservas.show', $reserva) }}" class="btn btn-sm btn-outline">Ver</a>
                                <a href="{{ route('reservas.edit', $reserva) }}" class="btn btn-sm btn-primary">Editar</a>
                                <form action="{{ route('reservas.destroy', $reserva) }}" method="POST" style="display:inline;" onsubmit="return confirm('¿Está seguro?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">No hay reservas registradas</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">
            {{ $reservas->links() }}
        </div>
    </div>
</x-app-layout>
