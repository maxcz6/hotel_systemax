<x-app-layout">
    <div class="container">
        <div class="page-header">
            <h1>Registrar Pago</h1>
        </div>

        <div class="card">
            <div class="card-body">
                <h3>Reserva #{{ $reserva->id }}</h3>
                <p><strong>Cliente:</strong> {{ $reserva->cliente->nombre }} {{ $reserva->cliente->apellido }}</p>
                <p><strong>Habitación:</strong> {{ $reserva->habitacion->numero }}</p>
                <p><strong>Saldo Pendiente:</strong> ${{ number_format($saldoPendiente, 2) }}</p>
            </div>
        </div>

        <form action="{{ route('pagos.store') }}" method="POST">
            @csrf
            <input type="hidden" name="id_reserva" value="{{ $reserva->id }}">

            <div class="card">
                <div class="card-body">
                    <div class="form-group">
                        <label for="monto" class="form-label">Monto</label>
                        <input type="number" name="monto" id="monto" value="{{ old('monto', $saldoPendiente) }}" step="0.01" min="0.01" class="form-control" required>
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
                        <label for="referencia" class="form-label">Referencia (Opcional)</label>
                        <input type="text" name="referencia" id="referencia" value="{{ old('referencia') }}" maxlength="100" class="form-control">
                        @error('referencia')
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
    </div>
</x-app-layout>
