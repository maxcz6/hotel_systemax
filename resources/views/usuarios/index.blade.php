<x-app-layout>
    <x-slot name="header">
        <h2>{{ __('Gestión de Usuarios') }}</h2>
    </x-slot>

    <div class="container">
        <div class="card">
            <div class="card-header">
                <h3>Lista de Usuarios del Sistema</h3>
            </div>
            <div class="card-body">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Rol</th>
                            <th>Fecha de Registro</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($usuarios as $usuario)
                        <tr>
                            <td>{{ $usuario->id }}</td>
                            <td>{{ $usuario->name }}</td>
                            <td>{{ $usuario->email }}</td>
                            <td>
                                <span class="badge badge-{{ $usuario->role }}">
                                    @if($usuario->role === 'administrador')
                                        Administrador
                                    @elseif($usuario->role === 'gerente')
                                        Gerente
                                    @elseif($usuario->role === 'recepcion')
                                        Recepción
                                    @elseif($usuario->role === 'limpieza')
                                        Limpieza
                                    @elseif($usuario->role === 'mantenimiento')
                                        Mantenimiento
                                    @else
                                        {{ $usuario->role }}
                                    @endif
                                </span>
                            </td>
                            <td>{{ $usuario->created_at->format('d/m/Y') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <style>
        .badge {
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-weight: bold;
            display: inline-block;
        }
        .badge-administrador {
            background: #fef3c7;
            color: #92400e;
        }
        .badge-gerente {
            background: #dbeafe;
            color: #1e40af;
        }
        .badge-recepcion {
            background: #d1fae5;
            color: #065f46;
        }
        .badge-limpieza {
            background: #e0e7ff;
            color: #3730a3;
        }
        .badge-mantenimiento {
            background: #fce7f3;
            color: #9f1239;
        }
    </style>
</x-app-layout>
