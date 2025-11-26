<x-app-layout>
    <div class="container">
        <div class="page-header">
            <h1>Nuevo Cliente</h1>
            <a href="{{ route('clientes.index') }}" class="btn btn-secondary">Volver</a>
        </div>

        <form action="{{ route('clientes.store') }}" method="POST">
            @csrf
            <div class="card">
                <div class="card-body">
                    <h3>Información Personal</h3>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="nombre" class="form-label">Nombre *</label>
                            <input type="text" 
                                   name="nombre" 
                                   id="nombre" 
                                   value="{{ old('nombre') }}" 
                                   class="form-control" 
                                   placeholder="Nombre del cliente"
                                   required>
                            @error('nombre')
                            <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="apellido" class="form-label">Apellido *</label>
                            <input type="text" 
                                   name="apellido" 
                                   id="apellido" 
                                   value="{{ old('apellido') }}" 
                                   class="form-control" 
                                   placeholder="Apellido del cliente"
                                   required>
                            @error('apellido')
                            <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="tipo_documento" class="form-label">Tipo de Documento *</label>
                            <select name="tipo_documento" id="tipo_documento" class="form-control" required>
                                <option value="">Seleccione tipo</option>
                                <option value="DNI" {{ old('tipo_documento') == 'DNI' ? 'selected' : '' }}>DNI</option>
                                <option value="Pasaporte" {{ old('tipo_documento') == 'Pasaporte' ? 'selected' : '' }}>Pasaporte</option>
                                <option value="Carnet Extranjería" {{ old('tipo_documento') == 'Carnet Extranjería' ? 'selected' : '' }}>Carnet de Extranjería</option>
                            </select>
                            @error('tipo_documento')
                            <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="numero_documento" class="form-label">Número de Documento *</label>
                            <input type="text" 
                                   name="numero_documento" 
                                   id="numero_documento" 
                                   value="{{ old('numero_documento') }}" 
                                   class="form-control" 
                                   placeholder="Número de documento"
                                   required>
                            @error('numero_documento')
                            <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <h3 style="margin-top: 1.5rem;">Información de Contacto</h3>

                    <div class="form-group">
                        <label for="email" class="form-label">Email *</label>
                        <input type="email" 
                               name="email" 
                               id="email" 
                               value="{{ old('email') }}" 
                               class="form-control" 
                               placeholder="correo@ejemplo.com"
                               required>
                        @error('email')
                        <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="telefono" class="form-label">Teléfono</label>
                        <input type="text" 
                               name="telefono" 
                               id="telefono" 
                               value="{{ old('telefono') }}" 
                               class="form-control" 
                               placeholder="+51 999 999 999">
                        @error('telefono')
                        <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="direccion" class="form-label">Dirección</label>
                        <textarea name="direccion" 
                                  id="direccion" 
                                  rows="2" 
                                  class="form-control" 
                                  placeholder="Dirección completa del cliente">{{ old('direccion') }}</textarea>
                        @error('direccion')
                        <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Guardar Cliente</button>
                        <a href="{{ route('clientes.index') }}" class="btn btn-secondary">Cancelar</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>
