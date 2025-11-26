<x-app-layout>
    <x-slot name="header">
        <h1>Reservas</h1>
    </x-slot>

    <a href="{{ route('reservas.create') }}" class="btn">Nueva Reserva</a>

    <table>
        <thead>
            <tr>
                <th>Cliente</th>
                <th>Habitación</th>
                <th>Fecha de Entrada</th>
                <th>Fecha de Salida</th>
                <th>Total</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($reservas as $reserva)
                <tr>
                    <td>{{ $reserva->cliente->nombre }} {{ $reserva->cliente->apellido }}</td>
                    <td>{{ $reserva->habitacion->numero }}</td>
                    <td>{{ $reserva->fecha_entrada }}</td>
                    <td>{{ $reserva->fecha_salida }}</td>
                    <td>{{ $reserva->total_precio }}</td>
                    <td>{{ $reserva->estado }}</td>
                    <td>
                        <a href="{{ route('reservas.show', $reserva) }}" class="btn btn-secondary">Ver</a>
                        <a href="{{ route('reservas.edit', $reserva) }}" class="btn">Editar</a>
                        <form action="{{ route('reservas.destroy', $reserva) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $reservas->links() }}
</x-app-layout>
