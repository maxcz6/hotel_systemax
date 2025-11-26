<x-app-layout>
    <x-slot name="header">
        <h1>Clientes</h1>
    </x-slot>

    <a href="{{ route('clientes.create') }}" class="btn">Nuevo Cliente</a>

    <table>
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Email</th>
                <th>Teléfono</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($clientes as $cliente)
                <tr>
                    <td>{{ $cliente->nombre }} {{ $cliente->apellido }}</td>
                    <td>{{ $cliente->email }}</td>
                    <td>{{ $cliente->telefono }}</td>
                    <td>
                        <a href="{{ route('clientes.show', $cliente) }}" class="btn btn-secondary">Ver</a>
                        <a href="{{ route('clientes.edit', $cliente) }}" class="btn">Editar</a>
                        <form action="{{ route('clientes.destroy', $cliente) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $clientes->links() }}
</x-app-layout>
