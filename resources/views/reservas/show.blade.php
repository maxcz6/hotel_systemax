<x-app-layout>
    <x-slot name="header">
        <h1>Detalles de la Reserva</h1>
    </x-slot>

    <div>
        <strong>Cliente:</strong> {{ $reserva->cliente->nombre }} {{ $reserva->cliente->apellido }}
    </div>
    <div>
        <strong>Habitación:</strong> {{ $reserva->habitacion->numero }}
    </div>
    <div>
        <strong>Fecha de Entrada:</strong> {{ $reserva->fecha_entrada }}
    </div>
    <div>
        <strong>Fecha de Salida:</strong> {{ $reserva->fecha_salida }}
    </div>
    <div>
        <strong>Total:</strong> {{ $reserva->total_precio }}
    </div>
    <div>
        <strong>Estado:</strong> {{ $reserva->estado }}
    </div>

    <a href="{{ route('reservas.index') }}" class="btn btn-secondary">Volver a la lista</a>
</x-app-layout>
