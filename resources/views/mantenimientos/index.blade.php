<x-app-layout>
    <div class="container">
        <div class="flex justify-between items-center mb-4">
            <h1>Mantenimiento</h1>
            @if(in_array(Auth::user()->role, ['administrador', 'gerente']))
                <a href="{{ route('mantenimientos.create') }}" class="btn btn-primary">+ Nuevo Mantenimiento</a>
            @endif
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card">
            <div class="card-header">Tareas de Mantenimiento</div>
            <div style="overflow-x: auto;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Habitación</th>
                            <th>Tipo</th>
                            <th>Descripción</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                            <th>Asignado</th>
                            <th style="text-align: right;">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($mantenimientos as $mant)
                        <tr>
                            <td><a href="{{ route('mantenimientos.show', $mant) }}" class="badge badge-primary">#{{ $mant->id }}</a></td>
                            <td><span class="badge badge-info">#{{ $mant->habitacion->numero }}</span></td>
                            <td>
                                @php
                                    $tipos = [
                                        'preventivo' => 'badge-info',
                                        'correctivo' => 'badge-warning',
                                        'urgente' => 'badge-danger'
                                    ];
                                    $clase = $tipos[$mant->tipo] ?? 'badge-outline';
                                @endphp
                                <span class="badge {{ $clase }}">{{ ucfirst($mant->tipo) }}</span>
                            </td>
                            <td>{{ Str::limit($mant->descripcion, 40) }}</td>
                            <td>
                                @php
                                    $estados = [
                                        'pendiente' => 'badge-outline',
                                        'en_progreso' => 'badge-warning',
                                        'completado' => 'badge-success'
                                    ];
                                    $clase = $estados[$mant->estado] ?? 'badge-outline';
                                @endphp
                                <span class="badge {{ $clase }}">{{ str_replace('_', ' ', ucfirst($mant->estado)) }}</span>
                            </td>
                            <td>{{ $mant->fecha_inicio->format('d/m') }}</td>
                            <td><small>{{ $mant->asignadoA->name ?? 'Sin asignar' }}</small></td>
                            <td style="text-align: right;">
                                <a href="{{ route('mantenimientos.show', $mant) }}" class="btn btn-sm btn-outline">Ver</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">No hay tareas de mantenimiento</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">
            {{ $mantenimientos->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
