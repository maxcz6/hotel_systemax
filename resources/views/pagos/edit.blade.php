<x-app-layout>
    <div class="container">
        <div class="page-header">
            <h1>Editar Pago #{{ $pago->id }}</h1>
        </div>

        <div class="card">
            <div class="card-body">
                <p><strong>Reserva:</strong> <a href="{{ route('reservas.show', $pago->reserva_id) }}">#{{ $pago->reserva_id }}</a></p>
                <p><strong>Cliente:</strong> {{ $pago->reserva->cliente->nombre }} {{ $pago->reserva->cliente->apellido }}</p>
                <p><strong>Creado por:</strong> {{ $pago->usuario ? $pago->usuario->name : 'N/A' }}</p>
                <p><strong>Fecha de creación:</strong> {{ $pago->created_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>

        <form action="{{ route('pagos.update', $pago->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="card">
                <div class="card-body">
                    <div class="form-group">
                        <label for="monto" class="form-label">Monto</label>
                        <input type="number" name="monto" id="monto" value="{{ old('monto', $pago->monto) }}" step="0.01" min="0.01" class="form-control" required>
                        @error('monto')
                        <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="metodo_pago" class="form-label">Método de Pago</label>
                        <select name="metodo_pago" id="metodo_pago" class="form-control" required>
                            <option value="">Seleccione método</option>
                            <option value="efectivo" {{ old('metodo_pago', $pago->metodo_pago) == 'efectivo' ? 'selected' : '' }}>Efectivo</option>
                            <option value="tarjeta" {{ old('metodo_pago', $pago->metodo_pago) == 'tarjeta' ? 'selected' : '' }}>Tarjeta</option>
                            <option value="transferencia" {{ old('metodo_pago', $pago->metodo_pago) == 'transferencia' ? 'selected' : '' }}>Transferencia</option>
                        </select>
                        @error('metodo_pago')
                        <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="numero_transaccion" class="form-label">Número de Transacción (Opcional)</label>
                        <input type="text" name="numero_transaccion" id="numero_transaccion" value="{{ old('numero_transaccion', $pago->numero_transaccion) }}" maxlength="100" class="form-control">
                        @error('numero_transaccion')
                        <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="comprobante" class="form-label">Comprobante (Opcional)</label>
                        <input type="text" name="comprobante" id="comprobante" value="{{ old('comprobante', $pago->comprobante) }}" maxlength="255" class="form-control">
                        @error('comprobante')
                        <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="estado" class="form-label">Estado</label>
                        <select name="estado" id="estado" class="form-control" required>
                            <option value="pendiente" {{ old('estado', $pago->estado) == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                            <option value="completado" {{ old('estado', $pago->estado) == 'completado' ? 'selected' : '' }}>Completado</option>
                            <option value="anulado" {{ old('estado', $pago->estado) == 'anulado' ? 'selected' : '' }}>Anulado</option>
                        </select>
                        @error('estado')
                        <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="fecha_pago" class="form-label">Fecha de Pago</label>
                        <input type="datetime-local" name="fecha_pago" id="fecha_pago" value="{{ old('fecha_pago', $pago->fecha_pago->format('Y-m-d\TH:i')) }}" class="form-control" required>
                        @error('fecha_pago')
                        <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Actualizar Pago</button>
                        <a href="{{ route('pagos.index') }}" class="btn btn-secondary">Cancelar</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>
