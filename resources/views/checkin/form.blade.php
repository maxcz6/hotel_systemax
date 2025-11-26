<x-app-layout>
    <div class="container">
        <div class="page-header">
            <h1>Check-In - Reserva #{{ $reserva->id }}</h1>
        </div>

        <div class="card">
            <div class="card-body">
                <h3>Información del Cliente</h3>
                <p><strong>Nombre:</strong> {{ $reserva->cliente->nombre }} {{ $reserva->cliente->apellido }}</p>
                <p><strong>Documento:</strong> {{ $reserva->cliente->tipo_documento }}: {{ $reserva->cliente->numero_documento }}</p>
                <p><strong>Teléfono:</strong> {{ $reserva->cliente->telefono }}</p>
                <p><strong>Email:</strong> {{ $reserva->cliente->email }}</p>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h3>Información de la Reserva</h3>
                <p><strong>Habitación:</strong> {{ $reserva->habitacion->numero }} - {{ $reserva->habitacion->tipoHabitacion->nombre }}</p>
                <p><strong>Fecha Entrada:</strong> {{ \Carbon\Carbon::parse($reserva->fecha_entrada)->format('d/m/Y') }}</p>
                <p><strong>Fecha Salida:</strong> {{ \Carbon\Carbon::parse($reserva->fecha_salida)->format('d/m/Y') }}</p>
                <p><strong>Precio Total:</strong> ${{ number_format($reserva->precio_total, 2) }}</p>
                <p><strong>Estado:</strong> {{ ucfirst($reserva->estado) }}</p>
            </div>
        </div>

        <form action="{{ route('checkin.store', $reserva) }}" method="POST">
            @csrf
            <div class="card">
                <div class="card-body">
                    <h3>Realizar Check-In</h3>
                    
                    <div class="form-group">
                        <label for="observaciones" class="form-label">Observaciones</label>
                        <textarea name="observaciones" id="observaciones" rows="4" class="form-control"></textarea>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Confirmar Check-In</button>
                        <a href="{{ route('reservas.index') }}" class="btn btn-secondary">Cancelar</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>
