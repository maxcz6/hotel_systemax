<?php

namespace App\Http\Controllers;

use App\Models\Salida;
use App\Models\Reserva;
use App\Models\Habitacion;
use Illuminate\Http\Request;

class SalidaController extends Controller
{
    public function index()
    {
        $salidas = Salida::with(['reserva.cliente', 'habitacion', 'usuario'])
            ->orderBy('fecha_salida_real', 'desc')
            ->paginate(15);

        return view('salidas.index', compact('salidas'));
    }

    public function create(Request $request)
    {
        $reserva_id = $request->get('reserva_id');

        if ($reserva_id) {
            $reserva = Reserva::with(['cliente', 'habitacion', 'estancia.serviciosDetalle', 'pagos'])
                ->findOrFail($reserva_id);

            // Calcular montos
            $totalHabitacion = $reserva->total_precio ?? 0;
            $servicios = $reserva->estancia ? $reserva->estancia->serviciosDetalle->where('estado', '!=', 'anulado')->sum('subtotal') : 0;
            $totalGeneral = $totalHabitacion + $servicios;
            $totalPagado = $reserva->pagos()->where('estado', 'completado')->sum('monto');
            $saldoPendiente = $totalGeneral - $totalPagado;

            return view('salidas.create', compact('reserva', 'totalHabitacion', 'servicios', 'totalGeneral', 'totalPagado', 'saldoPendiente'));
        }

        // Si viene de index, mostrar todas las reservas con estado checkin
        $reservas = Reserva::with('cliente', 'habitacion')
            ->where('estado', 'checkin')
            ->get();

        return view('salidas.create', compact('reservas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'reserva_id' => 'required|exists:reservas,id',
            'monto_total' => 'required|numeric|min:0',
            'estado' => 'required|in:completado,con_pendientes',
            'notas' => 'nullable|string|max:500',
        ]);

        $reserva = Reserva::with(['habitacion', 'cliente'])->findOrFail($validated['reserva_id']);

        $salida = Salida::create([
            'reserva_id' => $validated['reserva_id'],
            'habitacion_id' => $reserva->habitacion_id,
            'cliente_id' => $reserva->cliente_id,
            'fecha_salida_real' => now(),
            'monto_total' => $validated['monto_total'],
            'estado' => $validated['estado'],
            'notas' => $validated['notas'] ?? null,
            'usuario_id' => auth()->id(),
        ]);

        // Actualizar estado de la reserva a checkout
        $reserva->update(['estado' => 'checkout']);

        // Actualizar estado de la habitación a disponible
        $reserva->habitacion->update(['estado' => 'disponible']);

        return redirect()->route('salidas.show', $salida)
            ->with('success', 'Salida registrada correctamente. Habitación liberada.');
    }

    public function show(Salida $salida)
    {
        $salida->load([
            'reserva.cliente',
            'reserva.estancia.serviciosDetalle',
            'reserva.pagos',
            'habitacion',
            'usuario'
        ]);

        // Calcular información financiera
        $totalHabitacion = $salida->reserva->total_precio ?? 0;
        $servicios = $salida->reserva->estancia ? $salida->reserva->estancia->serviciosDetalle->where('estado', '!=', 'anulado')->sum('subtotal') : 0;
        $totalGeneral = $totalHabitacion + $servicios;
        $totalPagado = $salida->reserva->pagos()->where('estado', 'completado')->sum('monto');
        $saldoPendiente = $totalGeneral - $totalPagado;

        return view('salidas.show', compact('salida', 'totalHabitacion', 'servicios', 'totalGeneral', 'totalPagado', 'saldoPendiente'));
    }

    public function edit(Salida $salida)
    {
        $salida->load('reserva');
        return view('salidas.edit', compact('salida'));
    }

    public function update(Request $request, Salida $salida)
    {
        $validated = $request->validate([
            'monto_total' => 'required|numeric|min:0',
            'estado' => 'required|in:completado,con_pendientes',
            'notas' => 'nullable|string|max:500',
        ]);

        $salida->update($validated);

        return redirect()->route('salidas.show', $salida)
            ->with('success', 'Salida actualizada correctamente');
    }

    public function destroy(Salida $salida)
    {
        // Solo administrador puede eliminar
        if (auth()->user()->role !== 'administrador') {
            abort(403, 'No tiene permisos para eliminar salidas');
        }

        $reserva_id = $salida->reserva_id;
        $habitacion_id = $salida->habitacion_id;

        $salida->delete();

        // Revertir estado de reserva y habitación
        $reserva = Reserva::find($reserva_id);
        if ($reserva) {
            $reserva->update(['estado' => 'checkin']);
        }

        $habitacion = Habitacion::find($habitacion_id);
        if ($habitacion) {
            $habitacion->update(['estado' => 'ocupada']);
        }

        return redirect()->route('salidas.index')
            ->with('success', 'Salida eliminada correctamente');
    }
}
