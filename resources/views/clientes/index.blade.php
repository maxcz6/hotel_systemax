<x-app-layout>
    <div class="container">
        <div class="page-header">
            <h1>Clientes</h1>
            <a href="{{ route('clientes.create') }}" class="btn btn-primary">Nuevo Cliente</a>
        </div>


        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <div class="card">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>DNI</th>
                        <th>Email</th>
                        <th>Teléfono</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($clientes as $cliente)
                        <tr>
                            <td>{{ $cliente->nombre }} {{ $cliente->apellido }}</td>
                            <td>{{ $cliente->dni ?? '-' }}</td>
                            <td>{{ $cliente->email }}</td>
                            <td>{{ $cliente->telefono ?? '-' }}</td>
                            <td class="actions">
                                <a href="{{ route('clientes.show', $cliente) }}" class="btn btn-sm btn-secondary">Ver</a>
                                <a href="{{ route('clientes.edit', $cliente) }}" class="btn btn-sm btn-primary">Editar</a>
                                <form action="{{ route('clientes.destroy', $cliente) }}" method="POST" style="display:inline;" onsubmit="return confirm('¿Está seguro de eliminar este cliente?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 2rem;">
                                No hay clientes registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top: 1rem;">
            {{ $clientes->links() }}
        </div>
    </div>
</x-app-layout>
