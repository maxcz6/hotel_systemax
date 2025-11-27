<x-app-layout>
    <div class="container">
        <div class="page-header">
            <h1>Detalle de Pago #{{ $pago->id }}</h1>
            <a href="{{ route('pagos.index') }}" class="btn btn-secondary">Volver a Pagos</a>
        </div>

        <div class="card">
            <div class="card-body">
                <h3>Información del Pago</h3>
                
                <p><strong>ID:</strong> {{ $pago->id }}</p>
                <p><strong>Monto:</strong> ${{ number_format($pago->monto, 2) }}</p>
                <p><strong>Método de Pago:</strong> {{ ucfirst($pago->metodo_pago) }}</p>
                <p><strong>Estado:</strong> 
                    <span class="badge badge-{{ $pago->estado === 'completado' ? 'success' : ($pago->estado === 'anulado' ? 'danger' : 'warning') }}">
                        {{ ucfirst($pago->estado) }}
                    </span>
                </p>
                <p><strong>Fecha de Pago:</strong> {{ $pago->fecha_pago->format('d/m/Y H:i') }}</p>
                <p><strong>Número de Transacción:</strong> {{ $pago->numero_transaccion ?? 'N/A' }}</p>
                <p><strong>Comprobante:</strong> {{ $pago->comprobante ?? 'N/A' }}</p>
                <p><strong>Registrado por:</strong> {{ $pago->usuario ? $pago->usuario->name : 'N/A' }}</p>
                <p><strong>Fecha de Registro:</strong> {{ $pago->created_at->format('d/m/Y H:i') }}</p>

                @if($pago->estado === 'anulado')
                    <hr>
                    <h4>Información de Anulación</h4>
                    <p><strong>Anulado por:</strong> {{ $pago->anulado_por ? \App\Models\User::find($pago->anulado_por)->name : 'N/A' }}</p>
                    <p><strong>Fecha de Anulación:</strong> {{ $pago->fecha_anulacion ? $pago->fecha_anulacion->format('d/m/Y H:i') : 'N/A' }}</p>
                    <p><strong>Motivo:</strong> {{ $pago->motivo_anulacion ?? 'N/A' }}</p>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h3>Información de la Reserva</h3>
                
                <p><strong>Reserva #:</strong> <a href="{{ route('reservas.show', $pago->reserva_id) }}">{{ $pago->reserva_id }}</a></p>
                <p><strong>Cliente:</strong> {{ $pago->reserva->cliente->nombre }} {{ $pago->reserva->cliente->apellido }}</p>
                <p><strong>Email:</strong> {{ $pago->reserva->cliente->email }}</p>
                <p><strong>Teléfono:</strong> {{ $pago->reserva->cliente->telefono ?? 'N/A' }}</p>
                <p><strong>Habitación:</strong> {{ $pago->reserva->habitacion->numero }}</p>
                <p><strong>Check-in:</strong> {{ $pago->reserva->fecha_entrada->format('d/m/Y') }}</p>
                <p><strong>Check-out:</strong> {{ $pago->reserva->fecha_salida->format('d/m/Y') }}</p>
                <p><strong>Estado de Reserva:</strong> {{ ucfirst($pago->reserva->estado) }}</p>
            </div>
        </div>

        @if(Auth::user()->role === 'administrador')
            <div class="card">
                <div class="card-body">
                    <h3>Acciones de Administrador</h3>
                    <a href="{{ route('pagos.edit', $pago->id) }}" class="btn btn-primary">Editar Pago</a>
                    
                    @if($pago->estado !== 'anulado')
                        <form action="{{ route('pagos.destroy', $pago->id) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('¿Está seguro de anular este pago?')">Anular Pago</button>
                        </form>
                    @endif
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
