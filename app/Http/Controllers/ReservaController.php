<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use App\Models\Cliente;
use App\Models\Habitacion;
use App\Http\Requests\StoreReservaRequest;
use App\Http\Requests\UpdateReservaRequest;
use Carbon\Carbon;

class ReservaController extends Controller
{
    /**
     * Muestra un listado del recurso.
     */
    public function index()
    {
        $reservas = Reserva::with(['cliente', 'habitacion'])->paginate(10);
        return view('reservas.index', compact('reservas'));
    }

    /**
     * Muestra el formulario para crear un nuevo recurso.
     */
    public function create()
    {
        $clientes = Cliente::all();
        $habitaciones = Habitacion::where('estado', 'disponible')->get();
        return view('reservas.create', compact('clientes', 'habitaciones'));
    }

    /**
     * Almacena un recurso recién creado en el almacenamiento.
     */
    public function store(StoreReservaRequest $request)
    {
        $validatedData = $request->validated();

        $fechaEntrada = Carbon::parse($validatedData['fecha_entrada']);
        $fechaSalida = Carbon::parse($validatedData['fecha_salida']);
        $noches = $fechaSalida->diffInDays($fechaEntrada);

        $habitacion = Habitacion::findOrFail($validatedData['habitacion_id']);
        $totalPrecio = $noches * $habitacion->precio_por_noche;

        $validatedData['total_precio'] = $totalPrecio;

        Reserva::create($validatedData);

        return redirect()->route('reservas.index')->with('success', 'Reserva creada con éxito.');
    }

    /**
     * Muestra el recurso especificado.
     */
    public function show(Reserva $reserva)
    {
        $reserva->load([
            'cliente',
            'habitacion.tipoHabitacion',
            'estancia.serviciosDetalle.servicio',
            'pagos'
        ]);
        
        return view('reservas.show', compact('reserva'));
    }

    /**
     * Muestra el formulario para editar el recurso especificado.
     */
    public function edit(Reserva $reserva)
    {
        $clientes = Cliente::all();
        $habitaciones = Habitacion::all(); // Or more complex logic to show available rooms
        return view('reservas.edit', compact('reserva', 'clientes', 'habitaciones'));
    }

    /**
     * Actualiza el recurso especificado en el almacenamiento.
     */
    public function update(UpdateReservaRequest $request, Reserva $reserva)
    {
        $validatedData = $request->validated();

        $fechaEntrada = Carbon::parse($validatedData['fecha_entrada']);
        $fechaSalida = Carbon::parse($validatedData['fecha_salida']);
        $noches = $fechaSalida->diffInDays($fechaEntrada);

        $habitacion = Habitacion::findOrFail($validatedData['habitacion_id']);
        $totalPrecio = $noches * $habitacion->precio_por_noche;

        $validatedData['total_precio'] = $totalPrecio;

        $reserva->update($validatedData);

        return redirect()->route('reservas.index')->with('success', 'Reserva actualizada con éxito.');
    }

    /**
     * Elimina el recurso especificado del almacenamiento.
     */
    /**
     * Elimina el recurso especificado del almacenamiento.
     */
    public function destroy(Reserva $reserva)
    {
        // Solo el administrador puede eliminar físicamente
        if (auth()->user()->role !== 'administrador') {
            return redirect()->back()->with('error', 'Solo el administrador puede eliminar reservas permanentemente. Utilice la opción de cancelar.');
        }

        $reserva->delete();
        return redirect()->route('reservas.index')->with('success', 'Reserva eliminada permanentemente.');
    }

    /**
     * Cancela la reserva.
     */
    public function cancelar(\Illuminate\Http\Request $request, Reserva $reserva)
    {
        // Validar permisos (Admin, Gerente, Recepcion)
        if (!in_array(auth()->user()->role, ['administrador', 'gerente', 'recepcion'])) {
            abort(403, 'No tiene permisos para cancelar reservas.');
        }

        $request->validate([
            'motivo_cancelacion' => 'required|string|max:255',
        ]);

        $reserva->update([
            'estado' => 'cancelada',
            'cancelado_por' => auth()->id(),
            'fecha_cancelacion' => now(),
            'motivo_cancelacion' => $request->motivo_cancelacion,
        ]);

        // Liberar habitación si estaba ocupada
        if ($reserva->habitacion && $reserva->habitacion->estado === 'ocupada') {
            $reserva->habitacion->update(['estado' => 'disponible']);
        }

        return redirect()->route('reservas.show', $reserva)
            ->with('success', 'Reserva cancelada correctamente.');
    }
}
