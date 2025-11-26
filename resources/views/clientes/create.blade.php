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
                                   class="form-control @error('nombre') is-invalid @enderror" 
                                   placeholder="Nombre del cliente"
                                   required
                                   minlength="2"
                                   maxlength="255">
                            @error('nombre')
                            <span class="error-message">{{ $message }}</span>
                            @enderror
                            <small class="form-text">Solo letras y espacios, mínimo 2 caracteres</small>
                        </div>

                        <div class="form-group">
                            <label for="apellido" class="form-label">Apellido *</label>
                            <input type="text" 
                                   name="apellido" 
                                   id="apellido" 
                                   value="{{ old('apellido') }}" 
                                   class="form-control @error('apellido') is-invalid @enderror" 
                                   placeholder="Apellido del cliente"
                                   required
                                   minlength="2"
                                   maxlength="255">
                            @error('apellido')
                            <span class="error-message">{{ $message }}</span>
                            @enderror
                            <small class="form-text">Solo letras y espacios, mínimo 2 caracteres</small>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="dni" class="form-label">DNI</label>
                        <input type="text" 
                               name="dni" 
                               id="dni" 
                               value="{{ old('dni') }}" 
                               class="form-control @error('dni') is-invalid @enderror" 
                               placeholder="DNI de 8 dígitos"
                               maxlength="8"
                               pattern="[0-9]{8}">
                        @error('dni')
                        <span class="error-message">{{ $message }}</span>
                        @enderror
                        <small class="form-text">DNI peruano de 8 dígitos (opcional)</small>
                    </div>

                    <h3 style="margin-top: 1.5rem;">Información de Contacto</h3>

                    <div class="form-group">
                        <label for="email" class="form-label">Email *</label>
                        <input type="email" 
                               name="email" 
                               id="email" 
                               value="{{ old('email') }}" 
                               class="form-control @error('email') is-invalid @enderror" 
                               placeholder="correo@ejemplo.com"
                               required
                               maxlength="255">
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
                               class="form-control @error('telefono') is-invalid @enderror" 
                               placeholder="999999999"
                               maxlength="9"
                               pattern="[9][0-9]{8}">
                        @error('telefono')
                        <span class="error-message">{{ $message }}</span>
                        @enderror
                        <small class="form-text">Celular de 9 dígitos que empiece con 9 (opcional)</small>
                    </div>

                    <div class="form-group">
                        <label for="direccion" class="form-label">Dirección</label>
                        <textarea name="direccion" 
                                  id="direccion" 
                                  rows="2" 
                                  class="form-control @error('direccion') is-invalid @enderror" 
                                  placeholder="Dirección completa del cliente"
                                  maxlength="500">{{ old('direccion') }}</textarea>
                        @error('direccion')
                        <span class="error-message">{{ $message }}</span>
                        @enderror
                        <small class="form-text">Mínimo 5 caracteres (opcional)</small>
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
