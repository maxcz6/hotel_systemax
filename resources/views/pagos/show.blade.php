<x-app-layout>
    <div class="container">
        <div class="page-header">
            <h1>Detalle de Pago #{{ $pago->id }}</h1>
            <a href="{{ route('pagos.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver a Pagos
            </a>
        </div>

        <div class="row">
            <div class="col-md-8">
                {{-- Información del Pago --}}
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">
                            <i class="fas fa-money-bill-wave"></i> Información del Pago
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>ID:</strong> <span class="badge badge-primary">#{{ $pago->id }}</span></p>
                                <p><strong>Monto Pagado:</strong> <span class="h5 text-success">${{ number_format($pago->monto, 2) }}</span></p>
                                <p><strong>Método de Pago:</strong> 
                                    @if($pago->metodo_pago === 'efectivo')
                                        <span class="badge badge-info">💵 Efectivo</span>
                                    @elseif($pago->metodo_pago === 'tarjeta')
                                        <span class="badge badge-info">💳 Tarjeta</span>
                                    @elseif($pago->metodo_pago === 'transferencia')
                                        <span class="badge badge-info">🏦 Transferencia</span>
                                    @endif
                                </p>
                                <p><strong>Estado:</strong> 
                                    <span class="badge badge-{{ $pago->estado === 'completado' ? 'success' : ($pago->estado === 'anulado' ? 'danger' : 'warning') }}">
                                        {{ ucfirst($pago->estado) }}
                                    </span>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Fecha de Pago:</strong> {{ $pago->fecha_pago->format('d/m/Y H:i') }}</p>
                                <p><strong>Número de Transacción:</strong> <code>{{ $pago->numero_transaccion ?? 'N/A' }}</code></p>
                                <p><strong>Comprobante:</strong> <code>{{ $pago->comprobante ?? 'N/A' }}</code></p>
                                <p><strong>Registrado por:</strong> {{ $pago->usuario ? $pago->usuario->name : 'N/A' }}</p>
                            </div>
                        </div>

                        @if($pago->estado === 'anulado')
                            <hr>
                            <div class="alert alert-danger">
                                <h5><i class="fas fa-ban"></i> Información de Anulación</h5>
                                <p class="mb-1"><strong>Anulado por:</strong> {{ $pago->anulado_por ? \App\Models\User::find($pago->anulado_por)->name : 'N/A' }}</p>
                                <p class="mb-1"><strong>Fecha de Anulación:</strong> {{ $pago->fecha_anulacion ? $pago->fecha_anulacion->format('d/m/Y H:i') : 'N/A' }}</p>
                                <p class="mb-0"><strong>Motivo:</strong> {{ $pago->motivo_anulacion ?? 'N/A' }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Información de la Reserva --}}
                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        <h4 class="mb-0">
                            <i class="fas fa-hotel"></i> Información de la Reserva
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Reserva #:</strong> <a href="{{ route('reservas.show', $pago->reserva_id) }}" class="badge badge-secondary">#{{ $pago->reserva_id }}</a></p>
                                <p><strong>Cliente:</strong> {{ $pago->reserva->cliente->nombre }} {{ $pago->reserva->cliente->apellido }}</p>
                                <p><strong>Email:</strong> <a href="mailto:{{ $pago->reserva->cliente->email }}">{{ $pago->reserva->cliente->email }}</a></p>
                                <p><strong>Teléfono:</strong> {{ $pago->reserva->cliente->telefono ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Habitación:</strong> <span class="badge badge-secondary">#{{ $pago->reserva->habitacion->numero }}</span></p>
                                <p><strong>Check-in:</strong> {{ $pago->reserva->fecha_entrada->format('d/m/Y') }}</p>
                                <p><strong>Check-out:</strong> {{ $pago->reserva->fecha_salida->format('d/m/Y') }}</p>
                                <p><strong>Estado de Reserva:</strong> 
                                    <span class="badge badge-{{ $pago->reserva->estado === 'checkin' ? 'success' : ($pago->reserva->estado === 'cancelada' ? 'danger' : 'primary') }}">
                                        {{ ucfirst($pago->reserva->estado) }}
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Panel Lateral: Resumen de Costos --}}
            <div class="col-md-4">
                <div class="card sticky-top" style="top: 20px;">
                    <div class="card-header bg-success text-white">
                        <h4 class="mb-0">
                            <i class="fas fa-calculator"></i> Resumen Financiero
                        </h4>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <tr>
                                <td><strong>Habitación:</strong></td>
                                <td class="text-right">${{ number_format($pago->reserva->total_precio, 2) }}</td>
                            </tr>
                            @php
                                $servicios = $pago->reserva->estancia ? $pago->reserva->estancia->serviciosDetalle->where('estado', '!=', 'anulado')->sum('subtotal') : 0;
                            @endphp
                            <tr>
                                <td><strong>Servicios:</strong></td>
                                <td class="text-right">${{ number_format($servicios, 2) }}</td>
                            </tr>
                            <tr class="table-active border-top border-bottom">
                                <td><strong>TOTAL RESERVA:</strong></td>
                                <td class="text-right"><strong>${{ number_format($pago->reserva->total_precio + $servicios, 2) }}</strong></td>
                            </tr>
                            @php
                                $totalPagado = $pago->reserva->pagos()->where('estado', 'completado')->sum('monto');
                                $saldoPendiente = ($pago->reserva->total_precio + $servicios) - $totalPagado;
                            @endphp
                            <tr class="table-success">
                                <td><strong>Total Pagado:</strong></td>
                                <td class="text-right"><strong class="text-success">${{ number_format($totalPagado, 2) }}</strong></td>
                            </tr>
                            <tr class="table-{{ $saldoPendiente > 0 ? 'danger' : 'success' }}">
                                <td><strong>Saldo Pendiente:</strong></td>
                                <td class="text-right">
                                    <strong class="text-{{ $saldoPendiente > 0 ? 'danger' : 'success' }}">
                                        @if($saldoPendiente > 0)
                                            -${{ number_format($saldoPendiente, 2) }}
                                        @else
                                            ✓ PAGADO
                                        @endif
                                    </strong>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                @if(Auth::user()->role === 'administrador')
                    <div class="card mt-3">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0">
                                <i class="fas fa-cog"></i> Acciones
                            </h5>
                        </div>
                        <div class="card-body d-flex gap-2">
                            <a href="{{ route('pagos.edit', $pago->id) }}" class="btn btn-primary flex-grow-1">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                            
                            @if($pago->estado !== 'anulado')
                                <form action="{{ route('pagos.destroy', $pago->id) }}" method="POST" class="flex-grow-1">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger w-100" onclick="return confirm('¿Está seguro de anular este pago?')">
                                        <i class="fas fa-trash"></i> Anular
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
