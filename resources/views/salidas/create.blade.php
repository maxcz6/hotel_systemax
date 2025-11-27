<x-app-layout>
    <div class="container">
        <div class="page-header">
            <h1>Registrar Salida de Cliente</h1>
        </div>

        @if(isset($reserva))
            {{-- Vista cuando viene desde una reserva específica --}}
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Detalles de la Reserva</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Reserva:</strong> #{{ $reserva->id }}</p>
                            <p><strong>Cliente:</strong> {{ $reserva->cliente->nombre }} {{ $reserva->cliente->apellido }}</p>
                            <p><strong>Habitación:</strong> #{{ $reserva->habitacion->numero }}</p>
                            <p><strong>Fecha Check-out:</strong> {{ $reserva->fecha_salida->format('d/m/Y') }}</p>
                        </div>
                        <div class="col-md-6">
                            <div class="alert alert-info">
                                <h5>Resumen de Costos:</h5>
                                <table class="table table-sm mb-0">
                                    <tr>
                                        <td><strong>Habitación:</strong></td>
                                        <td class="text-right">${{ number_format($totalHabitacion, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Servicios:</strong></td>
                                        <td class="text-right">${{ number_format($servicios, 2) }}</td>
                                    </tr>
                                    <tr class="table-active">
                                        <td><strong>TOTAL RESERVA:</strong></td>
                                        <td class="text-right"><strong>${{ number_format($totalGeneral, 2) }}</strong></td>
                                    </tr>
                                    <tr class="table-success">
                                        <td><strong>Total Pagado:</strong></td>
                                        <td class="text-right"><strong class="text-success">${{ number_format($totalPagado, 2) }}</strong></td>
                                    </tr>
                                    <tr class="table-{{ $saldoPendiente > 0 ? 'danger' : 'success' }}">
                                        <td><strong>Saldo Pendiente:</strong></td>
                                        <td class="text-right"><strong class="text-{{ $saldoPendiente > 0 ? 'danger' : 'success' }}">${{ number_format($saldoPendiente, 2) }}</strong></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <form action="{{ route('salidas.store') }}" method="POST">
                @csrf
                <input type="hidden" name="reserva_id" value="{{ $reserva->id }}">

                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h4 class="mb-0">Información de Salida</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="monto_total" class="form-label"><strong>Monto Total a Cobrar</strong></label>
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text">$</span>
                                        <input type="number" name="monto_total" id="monto_total" value="{{ old('monto_total', $totalGeneral) }}" step="0.01" min="0" class="form-control" required>
                                    </div>
                                    @error('monto_total')
                                    <span class="error-message">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="estado" class="form-label"><strong>Estado de Pago</strong></label>
                                    <select name="estado" id="estado" class="form-control form-control-lg" required>
                                        <option value="completado" {{ old('estado') == 'completado' ? 'selected' : '' }}>Completado - Cliente Pagó Todo</option>
                                        <option value="con_pendientes" {{ old('estado') == 'con_pendientes' ? 'selected' : '' }}>Con Pendientes - Debe Dinero</option>
                                    </select>
                                    @error('estado')
                                    <span class="error-message">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="notas" class="form-label">Notas (Opcional)</label>
                            <textarea name="notas" id="notas" class="form-control" rows="3" placeholder="Ej: Daños en la habitación, reclamos, observaciones...">{{ old('notas') }}</textarea>
                            @error('notas')
                            <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <hr class="my-4">

                        <div class="form-actions">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-check-circle"></i> Registrar Salida
                            </button>
                            <a href="{{ route('reservas.show', $reserva) }}" class="btn btn-secondary btn-lg">
                                <i class="fas fa-times-circle"></i> Cancelar
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        @else
            {{-- Vista cuando viene desde index de salidas --}}
            <form action="{{ route('salidas.store') }}" method="POST">
                @csrf

                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h4 class="mb-0">Seleccionar Reserva</h4>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="reserva_id" class="form-label">Reserva (En Check-in)</label>
                            <select name="reserva_id" id="reserva_id" class="form-control form-control-lg" required onchange="window.location.href='{{ route('salidas.create') }}?reserva_id=' + this.value">
                                <option value="">-- Seleccione una reserva --</option>
                                @forelse($reservas as $res)
                                    <option value="{{ $res->id }}">
                                        #{{ $res->id }} - {{ $res->cliente->nombre }} {{ $res->cliente->apellido }} - Hab. {{ $res->habitacion->numero }}
                                    </option>
                                @empty
                                    <option disabled>No hay reservas con check-in activo</option>
                                @endforelse
                            </select>
                        </div>
                    </div>
                </div>
            </form>
        @endif
    </div>
</x-app-layout>
