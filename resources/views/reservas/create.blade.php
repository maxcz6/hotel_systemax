<x-app-layout>
    <x-slot name="header">
        <h1>Nueva Reserva</h1>
    </x-slot>

    <form action="{{ route('reservas.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="cliente_id">Cliente</label>
            <select name="cliente_id" id="cliente_id" required>
                @foreach ($clientes as $cliente)
                    <option value="{{ $cliente->id }}" {{ old('cliente_id') == $cliente->id ? 'selected' : '' }}>
                        {{ $cliente->nombre }} {{ $cliente->apellido }}
                    </option>
                @endforeach
            </select>
            @error('cliente_id')
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="habitacion_id">Habitación</label>
            <select name="habitacion_id" id="habitacion_id" required>
                @foreach ($habitaciones as $habitacion)
                    <option value="{{ $habitacion->id }}" {{ old('habitacion_id') == $habitacion->id ? 'selected' : '' }}>
                        {{ $habitacion->numero }} ({{ $habitacion->tipoHabitacion->nombre }})
                    </option>
                @endforeach
            </select>
            @error('habitacion_id')
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="fecha_entrada">Fecha de Entrada</label>
            <input type="date" name="fecha_entrada" id="fecha_entrada" value="{{ old('fecha_entrada') }}" required>
            @error('fecha_entrada')
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="fecha_salida">Fecha de Salida</label>
            <input type="date" name="fecha_salida" id="fecha_salida" value="{{ old('fecha_salida') }}" required>
            @error('fecha_salida')
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="estado">Estado</label>
            <select name="estado" id="estado" required>
                <option value="pendiente" {{ old('estado') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                <option value="confirmada" {{ old('estado') == 'confirmada' ? 'selected' : '' }}>Confirmada</option>
                <option value="cancelada" {{ old('estado') == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                <option value="check-in" {{ old('estado') == 'check-in' ? 'selected' : '' }}>Check-In</option>
                <option value="check-out" {{ old('estado') == 'check-out' ? 'selected' : '' }}>Check-Out</option>
            </select>
            @error('estado')
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn">Guardar Reserva</button>
    </form>
</x-app-layout>
