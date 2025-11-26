<x-app-layout>
    <x-slot name="header">
        <h1>Detalles del Cliente</h1>
    </x-slot>

    <div>
        <strong>Nombre:</strong> {{ $cliente->nombre }} {{ $cliente->apellido }}
    </div>
    <div>
        <strong>Email:</strong> {{ $cliente->email }}
    </div>
    <div>
        <strong>Teléfono:</strong> {{ $cliente->telefono }}
    </div>
    <div>
        <strong>Dirección:</strong> {{ $cliente->direccion }}
    </div>

    <a href="{{ route('clientes.index') }}" class="btn btn-secondary">Volver a la lista</a>
</x-app-layout>
