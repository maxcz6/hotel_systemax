<x-app-layout>
    <div class="container">
        <div class="page-header">
            <h1>Crear Mantenimiento</h1>
        </div>

        <form action="{{ route('mantenimientos.store') }}" method="POST">
            @csrf

            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Información del Mantenimiento</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="habitacion_id" class="form-label"><strong>Habitación</strong></label>
                                <select name="habitacion_id" id="habitacion_id" class="form-control form-control-lg" required>
                                    <option value="">-- Seleccione --</option>
                                    @foreach($habitaciones as $hab)
                                        <option value="{{ $hab->id }}" {{ old('habitacion_id') == $hab->id ? 'selected' : '' }}>
                                            #{{ $hab->numero }} ({{ $hab->tipoHabitacion->nombre }}) - {{ ucfirst($hab->estado) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('habitacion_id')
                                <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tipo" class="form-label"><strong>Tipo de Mantenimiento</strong></label>
                                <select name="tipo" id="tipo" class="form-control form-control-lg" required>
                                    <option value="">-- Seleccione --</option>
                                    <option value="preventivo" {{ old('tipo') == 'preventivo' ? 'selected' : '' }}>Preventivo</option>
                                    <option value="correctivo" {{ old('tipo') == 'correctivo' ? 'selected' : '' }}>Correctivo</option>
                                    <option value="urgente" {{ old('tipo') == 'urgente' ? 'selected' : '' }}>🚨 Urgente</option>
                                </select>
                                @error('tipo')
                                <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="descripcion" class="form-label"><strong>Descripción</strong></label>
                        <textarea name="descripcion" id="descripcion" class="form-control" rows="3" required>{{ old('descripcion') }}</textarea>
                        @error('descripcion')
                        <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="fecha_inicio" class="form-label"><strong>Fecha Inicio</strong></label>
                                <input type="date" name="fecha_inicio" id="fecha_inicio" value="{{ old('fecha_inicio') }}" class="form-control form-control-lg" required>
                                @error('fecha_inicio')
                                <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="asignado_a" class="form-label">Asignado A (Opcional)</label>
                                <select name="asignado_a" id="asignado_a" class="form-control form-control-lg">
                                    <option value="">-- Sin asignar --</option>
                                    @foreach($usuarios as $user)
                                        <option value="{{ $user->id }}" {{ old('asignado_a') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ ucfirst($user->role) }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="notas_tecnicas" class="form-label">Notas Técnicas (Opcional)</label>
                        <textarea name="notas_tecnicas" id="notas_tecnicas" class="form-control" rows="3">{{ old('notas_tecnicas') }}</textarea>
                        @error('notas_tecnicas')
                        <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="costo" class="form-label">Costo Estimado (Opcional)</label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text">$</span>
                            <input type="number" name="costo" id="costo" value="{{ old('costo') }}" step="0.01" min="0" class="form-control">
                        </div>
                        @error('costo')
                        <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <hr class="my-4">

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-plus-circle"></i> Crear Mantenimiento
                        </button>
                        <a href="{{ route('mantenimientos.index') }}" class="btn btn-secondary btn-lg">
                            <i class="fas fa-times-circle"></i> Cancelar
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>
