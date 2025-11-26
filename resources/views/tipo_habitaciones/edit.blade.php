<x-app-layout>
    <div class="container">
        <div class="page-header">
            <h1>Editar Tipo: {{ $tipoHabitacion->nombre }}</h1>
            <a href="{{ route('tipo_habitaciones.index') }}" class="btn btn-secondary">Volver</a>
        </div>

        <form action="{{ route('tipo_habitaciones.update', $tipoHabitacion->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="card">
                <div class="card-body">
                    <h3>Información del Tipo</h3>

                    <div class="form-group">
                        <label for="nombre" class="form-label">Nombre del Tipo *</label>
                        <input type="text" 
                               name="nombre" 
                               id="nombre" 
                               value="{{ old('nombre', $tipoHabitacion->nombre) }}" 
                               class="form-control" 
                               placeholder="Ej: Suite Presidencial, Habitación Doble, etc."
                               required>
                        @error('nombre')
                        <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea name="descripcion" 
                                  id="descripcion" 
                                  rows="3" 
                                  class="form-control" 
                                  placeholder="Descripción detallada del tipo de habitación">{{ old('descripcion', $tipoHabitacion->descripcion) }}</textarea>
                        @error('descripcion')
                        <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="capacidad" class="form-label">Capacidad</label>
                            <input type="number" 
                                   name="capacidad" 
                                   id="capacidad" 
                                   value="{{ old('capacidad', $tipoHabitacion->capacidad ?? 2) }}" 
                                   class="form-control" 
                                   min="1"
                                   max="20"
                                   placeholder="Número de personas">
                            @error('capacidad')
                            <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="precio_por_noche" class="form-label">Precio por Noche *</label>
                            <input type="number" 
                                   name="precio_por_noche" 
                                   id="precio_por_noche" 
                                   value="{{ old('precio_por_noche', $tipoHabitacion->precio_por_noche) }}" 
                                   class="form-control" 
                                   step="0.01"
                                   min="0"
                                   required>
                            @error('precio_por_noche')
                            <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    @if($tipoHabitacion->habitaciones->count() > 0)
                    <div class="alert alert-info" style="margin-top: 1rem;">
                        <strong>Nota:</strong> Este tipo tiene {{ $tipoHabitacion->habitaciones->count() }} habitación(es) asociada(s). 
Los cambios de precio afectarán las nuevas reservas.
                    </div>
                    @endif

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Actualizar Tipo de Habitación</button>
                        <a href="{{ route('tipo_habitaciones.index') }}" class="btn btn-secondary">Cancelar</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>
