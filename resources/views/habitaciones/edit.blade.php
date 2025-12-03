<x-app-layout>
    <div class="container">
        <div class="page-header">
            <h1>Editar Habitación #{{ $habitacion->numero }}</h1>
            <a href="{{ route('habitaciones.index') }}" class="btn btn-secondary">Volver</a>
        </div>

        <form action="{{ route('habitaciones.update', $habitacion) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="card">
                <div class="card-body">
                    <h3>Información de la Habitación</h3>

                    <div class="form-group">
                        <label for="numero" class="form-label">Número de Habitación *</label>
                        <input type="text" 
                               name="numero" 
                               id="numero" 
                               value="{{ old('numero', $habitacion->numero) }}" 
                               class="form-control" 
                               placeholder="Ej: 101, 201, etc."
                               required>
                        @error('numero')
                        <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="tipo_habitacion_id" class="form-label">Tipo de Habitación *</label>
                        <select name="tipo_habitacion_id" id="tipo_habitacion_id" class="form-control" required>
                            <option value="">Seleccione un tipo</option>
                            @foreach ($tipoHabitaciones as $tipo)
                                <option value="{{ $tipo->id }}" 
                                        {{ old('tipo_habitacion_id', $habitacion->tipo_habitacion_id) == $tipo->id ? 'selected' : '' }}>
                                    {{ $tipo->nombre }} - ${{ number_format($tipo->precio_por_noche, 2) }}/noche
                                </option>
                            @endforeach
                        </select>
                        @error('tipo_habitacion_id')
                        <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="piso" class="form-label">Piso</label>
                        <input type="number" 
                               name="piso" 
                               id="piso" 
                               value="{{ old('piso', $habitacion->piso ?? 1) }}" 
                               class="form-control" 
                               min="1"
                               placeholder="Número de piso">
                        @error('piso')
                        <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="precio_por_noche" class="form-label">Precio por Noche *</label>
                        <input type="number" 
                               name="precio_por_noche" 
                               id="precio_por_noche" 
                               value="{{ old('precio_por_noche', $habitacion->precio_por_noche) }}" 
                               class="form-control" 
                               step="0.01"
                               min="0"
                               required>
                        @error('precio_por_noche')
                        <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="estado" class="form-label">Estado *</label>
                        <select name="estado" id="estado" class="form-control" required>
                            <option value="disponible" {{ old('estado', $habitacion->estado) == 'disponible' ? 'selected' : '' }}>Disponible</option>
                            <option value="limpieza" {{ old('estado', $habitacion->estado) == 'limpieza' ? 'selected' : '' }}>En Limpieza</option>
                            <option value="mantenimiento" {{ old('estado', $habitacion->estado) == 'mantenimiento' ? 'selected' : '' }}>En Mantenimiento</option>
                            <option value="ocupada" {{ old('estado', $habitacion->estado) == 'ocupada' ? 'selected' : '' }}>Ocupada</option>
                        </select>
                        @error('estado')
                        <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea name="descripcion" 
                                  id="descripcion" 
                                  rows="3" 
                                  class="form-control" 
                                  placeholder="Características adicionales de la habitación...">{{ old('descripcion', $habitacion->descripcion ?? '') }}</textarea>
                        @error('descripcion')
                        <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Actualizar Habitación</button>
                        <a href="{{ route('habitaciones.index') }}" class="btn btn-secondary">Cancelar</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>
