<x-app-layout>
    <div class="container">
        <div class="page-header">
            <h1>Editar Salida #{{ $salida->id }}</h1>
        </div>

        <form action="{{ route('salidas.update', $salida) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="monto_total" class="form-label"><strong>Monto Total</strong></label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text">$</span>
                                    <input type="number" name="monto_total" id="monto_total" value="{{ old('monto_total', $salida->monto_total) }}" step="0.01" min="0" class="form-control" required>
                                </div>
                                @error('monto_total')
                                <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="estado" class="form-label"><strong>Estado</strong></label>
                                <select name="estado" id="estado" class="form-control form-control-lg" required>
                                    <option value="completado" {{ old('estado', $salida->estado) == 'completado' ? 'selected' : '' }}>Completado</option>
                                    <option value="con_pendientes" {{ old('estado', $salida->estado) == 'con_pendientes' ? 'selected' : '' }}>Con Pendientes</option>
                                </select>
                                @error('estado')
                                <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="notas" class="form-label">Notas</label>
                        <textarea name="notas" id="notas" class="form-control" rows="3">{{ old('notas', $salida->notas) }}</textarea>
                        @error('notas')
                        <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <hr class="my-4">

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save"></i> Guardar Cambios
                        </button>
                        <a href="{{ route('salidas.show', $salida) }}" class="btn btn-secondary btn-lg">
                            <i class="fas fa-times-circle"></i> Cancelar
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>
