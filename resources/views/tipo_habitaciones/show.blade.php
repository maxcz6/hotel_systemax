<x-app-layout>
    <x-slot name="header">
        <h1>Detalles del Tipo de Habitación</h1>
    </x-slot>

    <div>
        <strong>Nombre:</strong> {{ $tipoHabitacion->nombre }}
    </div>
    <div>
        <strong>Descripción:</strong> {{ $tipoHabitacion->descripcion }}
    </div>

    <a href="{{ route('tipo_habitaciones.index') }}" class="btn btn-secondary">Volver a la lista</a>
</x-app-layout>
