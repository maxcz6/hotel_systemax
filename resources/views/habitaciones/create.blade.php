<x-app-layout>
    <div class="container">
        <div class="page-header">
            <h1>Nueva Habitación</h1>
            <a href="{{ route('habitaciones.index') }}" class="btn btn-secondary">Volver</a>
        </div>

        <form action="{{ route('habitaciones.store') }}" method="POST">
            @csrf
            <div class="card">
                <div class="card-body">
                    <h3>Información de la Habitación</h3>

                    <div class="form-group">
                        <label for="numero" class="form-label">Número de Habitación *</label>
                        <input type="text" 
                               name="numero" 
                               id="numero" 
                               value="{{ old('numero') }}" 
                               class="form-control" 
                               placeholder="Ej: 101, 201, etc."
                               required>
                        @error('numero')
                        <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="tipo_habitacion_id" class="form-label">Tipo de Habitación *</label>
                        <select name="tipo_habitacion_id" id="tipo_habitacion_id" class="form-control" onchange="toggleNuevoTipo()">
                            <option value="">Seleccione un tipo</option>
                            @foreach ($tipoHabitaciones as $tipo)
                                <option value="{{ $tipo->id }}" {{ old('tipo_habitacion_id') == $tipo->id ? 'selected' : '' }}>
                                    {{ $tipo->nombre }} - ${{ number_format($tipo->precio_por_noche, 2) }}/noche
                                </option>
                            @endforeach
                            @if(Auth::user()->role === 'gerente')
                                <option value="nuevo">+ Crear Nuevo Tipo de Habitación</option>
                            @endif
                        </select>
                        @error('tipo_habitacion_id')
                        <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    @if(Auth::user()->role === 'gerente')
                    <!-- Campos para crear nuevo tipo de habitación (ocultos por defecto) -->
                    <div id="nuevoTipoFields" style="display: none; padding: 1rem; background-color: #f9fafb; border-radius: 0.375rem; margin-top: 1rem;">
                        <h4 style="margin-bottom: 1rem; color: #1f2937;">Nuevo Tipo de Habitación</h4>
                        
                        <div class="form-group">
                            <label for="nuevo_tipo_nombre" class="form-label">Nombre del Tipo</label>
                            <input type="text" 
                                   name="nuevo_tipo_nombre" 
                                   id="nuevo_tipo_nombre" 
                                   value="{{ old('nuevo_tipo_nombre') }}" 
                                   class="form-control" 
                                   placeholder="Ej: Suite Presidencial, Habitación Doble, etc.">
                            @error('nuevo_tipo_nombre')
                            <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="nuevo_tipo_descripcion" class="form-label">Descripción</label>
                            <textarea name="nuevo_tipo_descripcion" 
                                      id="nuevo_tipo_descripcion" 
                                      rows="2" 
                                      class="form-control" 
                                      placeholder="Descripción del tipo de habitación">{{ old('nuevo_tipo_descripcion') }}</textarea>
                            @error('nuevo_tipo_descripcion')
                            <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="nuevo_tipo_capacidad" class="form-label">Capacidad</label>
                                <input type="number" 
                                       name="nuevo_tipo_capacidad" 
                                       id="nuevo_tipo_capacidad" 
                                       value="{{ old('nuevo_tipo_capacidad', 2) }}" 
                                       class="form-control" 
                                       min="1"
                                       placeholder="Número de personas">
                                @error('nuevo_tipo_capacidad')
                                <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="nuevo_tipo_precio" class="form-label">Precio por Noche Base</label>
                                <input type="number" 
                                       name="nuevo_tipo_precio" 
                                       id="nuevo_tipo_precio" 
                                       value="{{ old('nuevo_tipo_precio') }}" 
                                       class="form-control" 
                                       step="0.01"
                                       min="0"
                                       placeholder="0.00">
                                @error('nuevo_tipo_precio')
                                <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="form-group">
                        <label for="piso" class="form-label">Piso</label>
                        <input type="number" 
                               name="piso" 
                               id="piso" 
                               value="{{ old('piso', 1) }}" 
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
                               value="{{ old('precio_por_noche') }}" 
                               class="form-control" 
                               step="0.01"
                               min="0"
                               placeholder="0.00"
                               required>
                        @error('precio_por_noche')
                        <span class="error-message">{{ $message }}</span>
                        @enderror
                        <small class="text-gray-600">El precio puede variar del tipo de habitación seleccionado</small>
                    </div>

                    <div class="form-group">
                        <label for="estado" class="form-label">Estado Inicial *</label>
                        <select name="estado" id="estado" class="form-control" required>
                            <option value="disponible" {{ old('estado', 'disponible') == 'disponible' ? 'selected' : '' }}>Disponible</option>
                            <option value="limpieza" {{ old('estado') == 'limpieza' ? 'selected' : '' }}>En Limpieza</option>
                            <option value="mantenimiento" {{ old('estado') == 'mantenimiento' ? 'selected' : '' }}>En Mantenimiento</option>
                            <option value="ocupada" {{ old('estado') == 'ocupada' ? 'selected' : '' }}>Ocupada</option>
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
                                  placeholder="Características adicionales de la habitación...">{{ old('descripcion') }}</textarea>
                        @error('descripcion')
                        <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Crear Habitación</button>
                        <a href="{{ route('habitaciones.index') }}" class="btn btn-secondary">Cancelar</a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @if(Auth::user()->role === 'gerente')
    <script>
        function toggleNuevoTipo() {
            const select = document.getElementById('tipo_habitacion_id');
            const nuevoTipoFields = document.getElementById('nuevoTipoFields');
            const selectElement = document.getElementById('tipo_habitacion_id');
            
            if (select.value === 'nuevo') {
                nuevoTipoFields.style.display = 'block';
                // Hacer los campos requeridos
                document.getElementById('nuevo_tipo_nombre').required = true;
                document.getElementById('nuevo_tipo_precio').required = true;
                // Remover required del select
                selectElement.required = false;
            } else {
                nuevoTipoFields.style.display = 'none';
                // Quitar required de los campos
                document.getElementById('nuevo_tipo_nombre').required = false;
                document.getElementById('nuevo_tipo_precio').required = false;
                // Restaurar required del select
                selectElement.required = true;
            }
        }

        // Ejecutar al cargar la página por si hay valores old()
        document.addEventListener('DOMContentLoaded', function() {
            toggleNuevoTipo();
        });
    </script>
    @endif
</x-app-layout>
