<x-app-layout>
    <div class="container">
        <div class="page-header">
            <h1>✏️ Editar Mantenimiento #{{ $mantenimiento->id }}</h1>
        </div>

        <form action="{{ route('mantenimientos.update', $mantenimiento) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tipo" class="form-label"><strong>Tipo</strong></label>
                                <select name="tipo" id="tipo" class="form-control form-control-lg" required>
                                    <option value="preventivo" {{ old('tipo', $mantenimiento->tipo) == 'preventivo' ? 'selected' : '' }}>Preventivo</option>
                                    <option value="correctivo" {{ old('tipo', $mantenimiento->tipo) == 'correctivo' ? 'selected' : '' }}>Correctivo</option>
                                    <option value="urgente" {{ old('tipo', $mantenimiento->tipo) == 'urgente' ? 'selected' : '' }}>Urgente</option>
                                </select>
                                @error('tipo')
                                <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="estado" class="form-label"><strong>Estado</strong></label>
                                <select name="estado" id="estado" class="form-control form-control-lg" required>
                                    <option value="pendiente" {{ old('estado', $mantenimiento->estado) == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                    <option value="en_progreso" {{ old('estado', $mantenimiento->estado) == 'en_progreso' ? 'selected' : '' }}>En Progreso</option>
                                    <option value="completado" {{ old('estado', $mantenimiento->estado) == 'completado' ? 'selected' : '' }}>Completado</option>
                                </select>
                                @error('estado')
                                <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="descripcion" class="form-label"><strong>Descripción</strong></label>
                        <textarea name="descripcion" id="descripcion" class="form-control" rows="3" required>{{ old('descripcion', $mantenimiento->descripcion) }}</textarea>
                        @error('descripcion')
                        <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="fecha_inicio" class="form-label"><strong>Fecha Inicio</strong></label>
                                <input type="date" name="fecha_inicio" id="fecha_inicio" value="{{ old('fecha_inicio', $mantenimiento->fecha_inicio->format('Y-m-d')) }}" class="form-control form-control-lg" required>
                                @error('fecha_inicio')
                                <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="fecha_fin" class="form-label">Fecha Fin (Opcional)</label>
                                <input type="date" name="fecha_fin" id="fecha_fin" value="{{ old('fecha_fin', $mantenimiento->fecha_fin?->format('Y-m-d')) }}" class="form-control form-control-lg">
                                @error('fecha_fin')
                                <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="asignado_a" class="form-label">Asignado A</label>
                        <select name="asignado_a" id="asignado_a" class="form-control form-control-lg">
                            <option value="">-- Sin asignar --</option>
                            @foreach($usuarios as $user)
                                <option value="{{ $user->id }}" {{ old('asignado_a', $mantenimiento->asignado_a) == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="notas_tecnicas" class="form-label">Notas Técnicas</label>
                        <textarea name="notas_tecnicas" id="notas_tecnicas" class="form-control" rows="3">{{ old('notas_tecnicas', $mantenimiento->notas_tecnicas) }}</textarea>
                    </div>

                    <div class="form-group">
                        <label for="costo" class="form-label">Costo</label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text">$</span>
                            <input type="number" name="costo" id="costo" value="{{ old('costo', $mantenimiento->costo) }}" step="0.01" min="0" class="form-control">
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save"></i> Guardar Cambios
                        </button>
                        <a href="{{ route('mantenimientos.show', $mantenimiento) }}" class="btn btn-secondary btn-lg">
                            <i class="fas fa-times-circle"></i> Cancelar
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>
