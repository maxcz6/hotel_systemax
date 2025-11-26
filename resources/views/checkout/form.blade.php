<x-app-layout>
    <div class="container">
        <div class="page-header">
            <h1>Check-Out - Reserva #{{ $reserva->id }}</h1>
        </div>

        <div class="card">
            <div class="card-body">
                <h3>Información del Cliente</h3>
                <p><strong>Nombre:</strong> {{ $reserva->cliente->nombre }} {{ $reserva->cliente->apellido }}</p>
                <p><strong>Documento:</strong> {{ $reserva->cliente->tipo_documento }}: {{ $reserva->cliente->numero_documento }}</p>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h3>Resumen de la Estancia</h3>
                <p><strong>Habitación:</strong> {{ $reserva->habitacion->numero }} - {{ $reserva->habitacion->tipoHabitacion->nombre }}</p>
                <p><strong>Fecha Entrada:</strong> {{ \Carbon\Carbon::parse($reserva->estancia->fecha_checkin)->format('d/m/Y H:i') }}</p>
                <p><strong>Fecha Salida:</strong> {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</p>
                <p><strong>Precio Base:</strong> ${{ number_format($reserva->precio_total, 2) }}</p>
            </div>
        </div>

        @if($reserva->estancia && $reserva->estancia->serviciosDetalle->count() > 0)
        <div class="card">
            <div class="card-body">
                <h3>Servicios Adicionales</h3>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Servicio</th>
                            <th>Cantidad</th>
                            <th>Precio Unitario</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $totalServicios = 0; @endphp
                        @foreach($reserva->estancia->serviciosDetalle as $detalle)
                        <tr>
                            <td>{{ $detalle->servicio->nombre }}</td>
                            <td>{{ $detalle->cantidad }}</td>
                            <td>${{ number_format($detalle->precio_unitario, 2) }}</td>
                            <td>${{ number_format($detalle->subtotal, 2) }}</td>
                        </tr>
                        @php $totalServicios += $detalle->subtotal; @endphp
                        @endforeach
                        <tr class="table-total">
                            <td colspan="3" class="text-right"><strong>Total Servicios:</strong></td>
                            <td><strong>${{ number_format($totalServicios, 2) }}</strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <div class="card">
            <div class="card-body">
                <h3>Pagos Realizados</h3>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Método</th>
                            <th>Monto</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $totalPagado = 0; @endphp
                        @foreach($reserva->pagos as $pago)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($pago->fecha)->format('d/m/Y H:i') }}</td>
                            <td>{{ ucfirst($pago->metodo_pago) }}</td>
                            <td>${{ number_format($pago->monto, 2) }}</td>
                        </tr>
                        @php $totalPagado += $pago->monto; @endphp
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <form action="{{ route('checkout.store', $reserva) }}" method="POST">
            @csrf
            <div class="card">
                <div class="card-body">
                    <h3>Total a Pagar</h3>
                    @php
                        $totalServicios = $reserva->estancia ? $reserva->estancia->serviciosDetalle->sum('subtotal') : 0;
                        $totalGeneral = $reserva->precio_total + $totalServicios;
                        $saldoPendiente = $totalGeneral - $totalPagado;
                    @endphp
                    <p><strong>Precio Base:</strong> ${{ number_format($reserva->precio_total, 2) }}</p>
                    <p><strong>Servicios Adicionales:</strong> ${{ number_format($totalServicios, 2) }}</p>
                    <p><strong>Total:</strong> ${{ number_format($totalGeneral, 2) }}</p>
                    <p><strong>Total Pagado:</strong> ${{ number_format($totalPagado, 2) }}</p>
                    <p class="alert alert-info"><strong>Saldo Pendiente:</strong> ${{ number_format($saldoPendiente, 2) }}</p>

                    @if($saldoPendiente > 0)
                    <div class="form-group">
                        <label for="metodo_pago" class="form-label">Método de Pago</label>
                        <select name="metodo_pago" id="metodo_pago" class="form-control" required>
                            <option value="">Seleccione método</option>
                            <option value="efectivo">Efectivo</option>
                            <option value="tarjeta">Tarjeta</option>
                            <option value="transferencia">Transferencia</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="monto" class="form-label">Monto a Pagar</label>
                        <input type="number" name="monto" id="monto" step="0.01" value="{{ $saldoPendiente }}" class="form-control" required>
                    </div>
                    @endif

                    <div class="form-group">
                        <label for="observaciones" class="form-label">Observaciones</label>
                        <textarea name="observaciones" id="observaciones" rows="3" class="form-control"></textarea>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Confirmar Check-Out</button>
                        <a href="{{ route('reservas.index') }}" class="btn btn-secondary">Cancelar</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>
