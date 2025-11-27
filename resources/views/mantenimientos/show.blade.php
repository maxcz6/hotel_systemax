<x-app-layout>
    <div class="container">
        <div class="page-header">
            <h1>Detalle de Mantenimiento #{{ $mantenimiento->id }}</h1>
            <a href="{{ route('mantenimientos.index') }}" class="btn btn-secondary">Volver a Mantenimientos</a>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0"><i class="fas fa-tools"></i> Información del Mantenimiento</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>ID:</strong> <span class="badge badge-primary">#{{ $mantenimiento->id }}</span></p>
                                <p><strong>Habitación:</strong> <span class="badge badge-secondary">#{{ $mantenimiento->habitacion->numero }}</span></p>
                                <p><strong>Tipo:</strong>
                                    @if($mantenimiento->tipo === 'preventivo')
                                        <span class="badge badge-info">Preventivo</span>
                                    @elseif($mantenimiento->tipo === 'correctivo')
                                        <span class="badge badge-warning">Correctivo</span>
                                    @else
                                        <span class="badge badge-danger">🚨 Urgente</span>
                                    @endif
                                </p>
                                <p><strong>Estado:</strong>
                                    @if($mantenimiento->estado === 'pendiente')
                                        <span class="badge badge-secondary">Pendiente</span>
                                    @elseif($mantenimiento->estado === 'en_progreso')
                                        <span class="badge badge-warning">En Progreso</span>
                                    @else
                                        <span class="badge badge-success">Completado</span>
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Fecha Inicio:</strong> {{ $mantenimiento->fecha_inicio->format('d/m/Y') }}</p>
                                @if($mantenimiento->fecha_fin)
                                    <p><strong>Fecha Fin:</strong> {{ $mantenimiento->fecha_fin->format('d/m/Y') }}</p>
                                @endif
                                <p><strong>Asignado A:</strong> {{ $mantenimiento->asignadoA->name ?? 'Sin asignar' }}</p>
                                @if($mantenimiento->costo)
                                    <p><strong>Costo:</strong> <span class="h5">${{ number_format($mantenimiento->costo, 2) }}</span></p>
                                @endif
                            </div>
                        </div>

                        <hr>

                        <h5>Descripción</h5>
                        <p>{{ $mantenimiento->descripcion }}</p>

                        @if($mantenimiento->notas_tecnicas)
                            <h5>Notas Técnicas</h5>
                            <p>{{ $mantenimiento->notas_tecnicas }}</p>
                        @endif
                    </div>
                </div>

                @if(Auth::user()->role === 'administrador' || Auth::user()->role === 'gerente')
                    <div class="card">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0"><i class="fas fa-cog"></i> Acciones</h5>
                        </div>
                        <div class="card-body d-flex gap-2 flex-column">
                            <a href="{{ route('mantenimientos.edit', $mantenimiento) }}" class="btn btn-primary">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                            <form action="{{ route('mantenimientos.destroy', $mantenimiento) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger w-100" onclick="return confirm('¿Está seguro de eliminar este mantenimiento?')">
                                    <i class="fas fa-trash"></i> Eliminar
                                </button>
                            </form>
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-md-4">
                <div class="card sticky-top" style="top: 20px;">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-home"></i> Habitación</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Número:</strong> {{ $mantenimiento->habitacion->numero }}</p>
                        <p><strong>Tipo:</strong> {{ $mantenimiento->habitacion->tipoHabitacion->nombre }}</p>
                        <p><strong>Estado:</strong> <span class="badge badge-{{ $mantenimiento->habitacion->estado === 'disponible' ? 'success' : 'warning' }}">{{ ucfirst($mantenimiento->habitacion->estado) }}</span></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
