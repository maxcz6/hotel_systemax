<x-app-layout>
    <div class="container">
        <div class="page-header">
            <h1>Registrar Pago</h1>
        </div>

        @if(isset($reserva))
            {{-- Vista cuando viene desde una reserva específica --}}
            <div class="card">
                <div class="card-body">
                    <h3>Reserva #{{ $reserva->id }}</h3>
                    <p><strong>Cliente:</strong> {{ $reserva->cliente->nombre }} {{ $reserva->cliente->apellido }}</p>
                    <p><strong>Habitación:</strong> {{ $reserva->habitacion->numero }}</p>
                    <p><strong>Total General:</strong> ${{ number_format($totalGeneral ?? 0, 2) }}</p>
                    <p><strong>Total Pagado:</strong> ${{ number_format($totalPagado ?? 0, 2) }}</p>
                    <p><strong>Saldo Pendiente:</strong> ${{ number_format($saldoPendiente ?? 0, 2) }}</p>
                </div>
            </div>

            <form action="{{ route('pagos.store') }}" method="POST">
                @csrf
                <input type="hidden" name="reserva_id" value="{{ $reserva->id }}">

                <div class="card">
                    <div class="card-body">
                        <div class="form-group">
                            <label for="monto" class="form-label">Monto</label>
                            <input type="number" name="monto" id="monto" value="{{ old('monto', $saldoPendiente ?? 0) }}" step="0.01" min="0.01" max="{{ $saldoPendiente ?? 999999 }}" class="form-control" required>
                            @error('monto')
                            <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="metodo_pago" class="form-label">Método de Pago</label>
                            <select name="metodo_pago" id="metodo_pago" class="form-control" required>
                                <option value="">Seleccione método</option>
                                <option value="efectivo" {{ old('metodo_pago') == 'efectivo' ? 'selected' : '' }}>Efectivo</option>
                                <option value="tarjeta" {{ old('metodo_pago') == 'tarjeta' ? 'selected' : '' }}>Tarjeta</option>
                                <option value="transferencia" {{ old('metodo_pago') == 'transferencia' ? 'selected' : '' }}>Transferencia</option>
                            </select>
                            @error('metodo_pago')
                            <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="numero_transaccion" class="form-label">Número de Transacción (Opcional)</label>
                            <input type="text" name="numero_transaccion" id="numero_transaccion" value="{{ old('numero_transaccion') }}" maxlength="100" class="form-control">
                            @error('numero_transaccion')
                            <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="comprobante" class="form-label">Comprobante (Opcional)</label>
                            <input type="text" name="comprobante" id="comprobante" value="{{ old('comprobante') }}" maxlength="255" class="form-control">
                            @error('comprobante')
                            <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">Registrar Pago</button>
                            <a href="{{ route('reservas.show', $reserva->id) }}" class="btn btn-secondary">Cancelar</a>
                        </div>
                    </div>
                </div>
            </form>
        @else
            {{-- Vista cuando viene desde index de pagos --}}
            <form action="{{ route('pagos.store') }}" method="POST">
                @csrf

                <div class="card">
                    <div class="card-body">
                        <div class="form-group">
                            <label for="reserva_id" class="form-label">Seleccionar Reserva</label>
                            <select name="reserva_id" id="reserva_id" class="form-control" required>
                                <option value="">Seleccione una reserva</option>
                                @foreach($reservas as $res)
                                    <option value="{{ $res->id }}" {{ old('reserva_id') == $res->id ? 'selected' : '' }}>
                                        #{{ $res->id }} - {{ $res->cliente->nombre }} {{ $res->cliente->apellido }} - Hab. {{ $res->habitacion->numero }} - ${{ number_format($res->total_precio, 2) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('reserva_id')
                            <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="monto" class="form-label">Monto</label>
                            <input type="number" name="monto" id="monto" value="{{ old('monto') }}" step="0.01" min="0.01" class="form-control" required>
                            @error('monto')
                            <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="metodo_pago" class="form-label">Método de Pago</label>
                            <select name="metodo_pago" id="metodo_pago" class="form-control" required>
                                <option value="">Seleccione método</option>
                                <option value="efectivo" {{ old('metodo_pago') == 'efectivo' ? 'selected' : '' }}>Efectivo</option>
                                <option value="tarjeta" {{ old('metodo_pago') == 'tarjeta' ? 'selected' : '' }}>Tarjeta</option>
                                <option value="transferencia" {{ old('metodo_pago') == 'transferencia' ? 'selected' : '' }}>Transferencia</option>
                            </select>
                            @error('metodo_pago')
                            <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="numero_transaccion" class="form-label">Número de Transacción (Opcional)</label>
                            <input type="text" name="numero_transaccion" id="numero_transaccion" value="{{ old('numero_transaccion') }}" maxlength="100" class="form-control">
                            @error('numero_transaccion')
                            <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="comprobante" class="form-label">Comprobante (Opcional)</label>
                            <input type="text" name="comprobante" id="comprobante" value="{{ old('comprobante') }}" maxlength="255" class="form-control">
                            @error('comprobante')
                            <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">Registrar Pago</button>
                            <a href="{{ route('pagos.index') }}" class="btn btn-secondary">Cancelar</a>
                        </div>
                    </div>
                </div>
            </form>
        @endif
    </div>
</x-app-layout>
