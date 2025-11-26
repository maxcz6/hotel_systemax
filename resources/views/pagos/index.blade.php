<x-app-layout>
    <div class="container">
        <div class="page-header">
            <h1>Pagos</h1>
            <a href="{{ route('reservas.index') }}" class="btn btn-primary">Volver a Reservas</a>
        </div>

        @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
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
                            <th>Referencia</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pagos as $pago)
                        <tr>
                            <td>{{ $pago->id }}</td>
                            <td><a href="{{ route('reservas.show', $pago->id_reserva) }}">#{{ $pago->id_reserva }}</a></td>
                            <td>{{ $pago->reserva->cliente->nombre }} {{ $pago->reserva->cliente->apellido }}</td>
                            <td>{{ \Carbon\Carbon::parse($pago->fecha)->format('d/m/Y H:i') }}</td>
                            <td>${{ number_format($pago->monto, 2) }}</td>
                            <td>{{ ucfirst($pago->metodo_pago) }}</td>
                            <td>{{ $pago->referencia ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">No hay pagos registrados</td>
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
