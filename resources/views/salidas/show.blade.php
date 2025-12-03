<x-app-layout>
    <div class="container">
        <div class="page-header">
            <h1>Detalle de Salida #{{ $salida->id }}</h1>
            <a href="{{ route('salidas.index') }}" class="btn btn-secondary">Volver a Salidas</a>
        </div>

        <div class="row">
            <div class="col-md-8">
                {{-- Información de la Salida --}}
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0"><i class="fas fa-sign-out-alt"></i> Información de Salida</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>ID Salida:</strong> <span class="badge badge-primary">#{{ $salida->id }}</span></p>
                                <p><strong>Fecha Salida:</strong> {{ $salida->fecha_salida_real->format('d/m/Y H:i') }}</p>
                                <p><strong>Monto Total:</strong> <span class="h5 text-success">${{ number_format($salida->monto_total, 2) }}</span></p>
                                <p><strong>Estado:</strong>
                                    @if($salida->estado === 'completado')
                                        <span class="badge badge-success">Completado</span>
                                    @else
                                        <span class="badge badge-warning">Con Pendientes</span>
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Registrado Por:</strong> {{ $salida->usuario->name ?? 'N/A' }}</p>
                                <p><strong>Habitación:</strong> <span class="badge badge-secondary">#{{ $salida->habitacion->numero }}</span></p>
                                <p><strong>Estado Habitación:</strong> <span class="badge badge-info">{{ ucfirst($salida->habitacion->estado) }}</span></p>
                                @if($salida->notas)
                                    <p><strong>Notas:</strong> {{ $salida->notas }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Información de la Reserva --}}
                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        <h4 class="mb-0"><i class="fas fa-hotel"></i> Información de la Reserva</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Reserva #:</strong> <a href="{{ route('reservas.show', $salida->reserva) }}" class="badge badge-secondary">#{{ $salida->reserva_id }}</a></p>
                                <p><strong>Cliente:</strong> {{ $salida->cliente->nombre }} {{ $salida->cliente->apellido }}</p>
                                <p><strong>Email:</strong> {{ $salida->cliente->email }}</p>
                                <p><strong>Teléfono:</strong> {{ $salida->cliente->telefono ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Check-in:</strong> {{ $salida->reserva->fecha_entrada->format('d/m/Y') }}</p>
                                <p><strong>Check-out:</strong> {{ $salida->reserva->fecha_salida->format('d/m/Y') }}</p>
                                <p><strong>Estado Reserva:</strong> <span class="badge badge-primary">{{ ucfirst($salida->reserva->estado) }}</span></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Panel Lateral: Resumen Financiero --}}
            <div class="col-md-4">
                <div class="card sticky-top" style="top: 20px;">
                    <div class="card-header bg-success text-white">
                        <h4 class="mb-0"><i class="fas fa-calculator"></i> Resumen Financiero</h4>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <tr>
                                <td><strong>Habitación:</strong></td>
                                <td class="text-right">${{ number_format($totalHabitacion, 2) }}</td>
                            </tr>
                            <tr>
                                <td><strong>Servicios:</strong></td>
                                <td class="text-right">${{ number_format($servicios, 2) }}</td>
                            </tr>
                            <tr class="table-active border-top border-bottom">
                                <td><strong>TOTAL RESERVA:</strong></td>
                                <td class="text-right"><strong>${{ number_format($totalGeneral, 2) }}</strong></td>
                            </tr>
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
                                            PAGADO
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
                            <h5 class="mb-0"><i class="fas fa-cog"></i> Acciones</h5>
                        </div>
                        <div class="card-body d-flex gap-2 flex-column">
                            <a href="{{ route('salidas.edit', $salida) }}" class="btn btn-primary">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                            <form action="{{ route('salidas.destroy', $salida) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger w-100" onclick="return confirm('¿Está seguro de eliminar esta salida?')">
                                    <i class="fas fa-trash"></i> Eliminar
                                </button>
                            </form>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
