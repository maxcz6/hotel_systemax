<x-app-layout>
    <x-slot name="header">
        <h2>{{ __('Editar Servicio') }}</h2>
    </x-slot>

    <div class="card">
        <form action="{{ route('servicios.update', $servicio) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="nombre" class="form-label">Nombre</label>
                <input type="text" name="nombre" id="nombre" class="form-input" value="{{ $servicio->nombre }}" required>
            </div>

            <div class="form-group">
                <label for="descripcion" class="form-label">Descripción</label>
                <textarea name="descripcion" id="descripcion" class="form-input">{{ $servicio->descripcion }}</textarea>
            </div>

            <div class="form-group">
                <label for="precio" class="form-label">Precio</label>
                <input type="number" step="0.01" name="precio" id="precio" class="form-input" value="{{ $servicio->precio }}" required>
            </div>

            <div class="flex justify-end gap-4">
                <a href="{{ route('servicios.index') }}" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn">Actualizar</button>
            </div>
        </form>
    </div>
</x-app-layout>
