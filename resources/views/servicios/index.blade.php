<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2>{{ __('Servicios') }}</h2>
            <a href="{{ route('servicios.create') }}" class="btn">{{ __('Nuevo Servicio') }}</a>
        </div>
    </x-slot>

    <div class="card">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Precio</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($servicios as $servicio)
                        <tr>
                            <td>{{ $servicio->nombre }}</td>
                            <td>{{ $servicio->descripcion }}</td>
                            <td>${{ number_format($servicio->precio, 2) }}</td>
                            <td>
                                <a href="{{ route('servicios.edit', $servicio) }}" class="action-btn">Editar</a>
                                <form action="{{ route('servicios.destroy', $servicio) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="delete-btn" onclick="return confirm('¿Estás seguro?')">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $servicios->links() }}
        </div>
    </div>
</x-app-layout>
