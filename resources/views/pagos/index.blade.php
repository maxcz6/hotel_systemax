<x-app-layout>
    <div class="container">
        <div class="page-header">
            <h1>Pagos</h1>
            <div>
                @if(in_array(Auth::user()->role, ['administrador', 'gerente', 'recepcion']))
                    <a href="{{ route('pagos.create') }}" class="btn btn-primary">Nuevo Pago</a>
                @endif
                <a href="{{ route('reservas.index') }}" class="btn btn-secondary">Volver a Reservas</a>
            </div>
        </div>

        @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="card">
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Reserva</th>
                            <th>Cliente</th>
                            <th>Fecha</th>
                            <th>Monto</th>
                            <th>Método</th>
                            <th>Estado</th>
                            <th>N° Transacción</th>
                            @if(Auth::user()->role === 'administrador')
                                <th>Acciones</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pagos as $pago)
                        <tr>
                            <td><a href="{{ route('pagos.show', $pago->id) }}">#{{ $pago->id }}</a></td>
                            <td><a href="{{ route('reservas.show', $pago->reserva_id) }}">#{{ $pago->reserva_id }}</a></td>
                            <td>{{ $pago->reserva->cliente->nombre }} {{ $pago->reserva->cliente->apellido }}</td>
                            <td>{{ $pago->fecha_pago->format('d/m/Y H:i') }}</td>
                            <td>${{ number_format($pago->monto, 2) }}</td>
                            <td>{{ ucfirst($pago->metodo_pago) }}</td>
                            <td>
                                <span class="badge badge-{{ $pago->estado === 'completado' ? 'success' : ($pago->estado === 'anulado' ? 'danger' : 'warning') }}">
                                    {{ ucfirst($pago->estado) }}
                                </span>
                            </td>
                            <td>{{ $pago->numero_transaccion ?? '-' }}</td>
                            @if(Auth::user()->role === 'administrador')
                                <td>
                                    <a href="{{ route('pagos.edit', $pago->id) }}" class="btn btn-sm btn-primary">Editar</a>
                                    @if($pago->estado !== 'anulado')
                                        <form action="{{ route('pagos.destroy', $pago->id) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Está seguro de anular este pago?')">Anular</button>
                                        </form>
                                    @endif
                                </td>
                            @endif
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ Auth::user()->role === 'administrador' ? '9' : '8' }}" class="text-center">No hay pagos registrados</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="pagination-wrapper">
                    {{ $pagos->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
