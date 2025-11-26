<x-app-layout>
    <x-slot name="header">
        <h1>Detalles de la Habitación</h1>
    </x-slot>

    <div>
        <strong>Número:</strong> {{ $habitacion->numero }}
    </div>
    <div>
        <strong>Tipo:</strong> {{ $habitacion->tipoHabitacion->nombre }}
    </div>
    <div>
        <strong>Precio por Noche:</strong> {{ $habitacion->precio_por_noche }}
    </div>
    <div>
        <strong>Estado:</strong> {{ $habitacion->estado }}
    </div>

    <a href="{{ route('habitaciones.index') }}" class="btn btn-secondary">Volver a la lista</a>
</x-app-layout>
