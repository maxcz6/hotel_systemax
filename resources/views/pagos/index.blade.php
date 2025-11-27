<x-app-layout>
    <div class="container">
        <div class="flex justify-between items-center mb-4">
            <h1>Pagos</h1>
            @if(in_array(Auth::user()->role, ['administrador', 'gerente', 'recepcion']))
                <a href="{{ route('pagos.create') }}" class="btn btn-primary">+ Nuevo Pago</a>
            @endif
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="card">
            <div style="overflow-x: auto;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Reserva</th>
                            <th>Cliente</th>
                            <th>Fecha</th>
                            <th>Monto</th>
                            <th>Total</th>
                            <th>Pagado</th>
                            <th>Pendiente</th>
                            <th>Método</th>
                            <th>Estado</th>
                            @if(Auth::user()->role === 'administrador')
                                <th style="text-align: right;">Acciones</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pagos as $pago)
                        <tr>
                            <td><a href="{{ route('pagos.show', $pago->id) }}" class="badge badge-primary">#{{ $pago->id }}</a></td>
                            <td><a href="{{ route('reservas.show', $pago->reserva_id) }}">#{{ $pago->reserva_id }}</a></td>
                            <td><strong>{{ $pago->reserva->cliente->nombre }}</strong></td>
                            <td>{{ $pago->fecha_pago->format('d/m') }}</td>
                            <td><span class="badge badge-info">${{ number_format($pago->monto, 2) }}</span></td>
                            <td>${{ number_format($pago->total_reserva, 2) }}</td>
                            <td><strong class="text-success">${{ number_format($pago->total_pagado, 2) }}</strong></td>
                            <td>
                                @if($pago->saldo_pendiente > 0)
                                    <strong class="text-danger">${{ number_format($pago->saldo_pendiente, 2) }}</strong>
                                @else
                                    <span class="badge badge-success">✓ Pagado</span>
                                @endif
                            </td>
                            <td><span class="badge badge-outline">{{ ucfirst($pago->metodo_pago) }}</span></td>
                            <td>
                                @php
                                    $estadoColor = [
                                        'completado' => 'badge-success',
                                        'pendiente' => 'badge-warning',
                                        'anulado' => 'badge-danger'
                                    ];
                                    $clase = $estadoColor[$pago->estado] ?? 'badge-info';
                                @endphp
                                <span class="badge {{ $clase }}">{{ ucfirst($pago->estado) }}</span>
                            </td>
                            @if(Auth::user()->role === 'administrador')
                            <td style="text-align: right;">
                                <a href="{{ route('pagos.show', $pago->id) }}" class="btn btn-sm btn-outline">Ver</a>
                                <a href="{{ route('pagos.edit', $pago->id) }}" class="btn btn-sm btn-primary">Editar</a>
                                @if($pago->estado !== 'anulado')
                                    <form action="{{ route('pagos.destroy', $pago->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('¿Está seguro?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Anular</button>
                                    </form>
                                @endif
                            </td>
                            @endif
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ Auth::user()->role === 'administrador' ? '11' : '10' }}" class="text-center text-muted">No hay pagos registrados</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">
            {{ $pagos->links() }}
        </div>
    </div>
</x-app-layout>
                                </span>
                            </td>
                            @if(Auth::user()->role === 'administrador')
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" id="dropdownMenu{{ $pago->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fas fa-cog"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenu{{ $pago->id }}">
                                            <li><a class="dropdown-item" href="{{ route('pagos.show', $pago->id) }}"><i class="fas fa-eye me-2"></i>Ver Detalles</a></li>
                                            <li><a class="dropdown-item" href="{{ route('pagos.edit', $pago->id) }}"><i class="fas fa-edit me-2"></i>Editar</a></li>
                                            @if($pago->estado !== 'anulado')
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <form action="{{ route('pagos.destroy', $pago->id) }}" method="POST" style="display: inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger" onclick="return confirm('¿Está seguro de anular este pago?')">
                                                            <i class="fas fa-trash me-2"></i>Anular Pago
                                                        </button>
                                                    </form>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </td>
                            @endif
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ Auth::user()->role === 'administrador' ? '11' : '10' }}" class="text-center">No hay pagos registrados</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="pagination-wrapper">
                    {{ $pagos->links() }}
                </div>
            </div>
        </div>
    </div>

    <style>
        .table-hover tbody tr:hover {
            background-color: #f5f5f5;
        }
        .badge-info {
            background-color: #17a2b8;
            color: white;
        }
        .badge-secondary {
            background-color: #6c757d;
            color: white;
        }
        .btn-group-sm {
            gap: 0.25rem;
        }
    </style>
</x-app-layout>
