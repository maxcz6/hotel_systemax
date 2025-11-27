<?php

namespace App\Http\Controllers;

use App\Models\Pago;
use App\Models\Reserva;
use Illuminate\Http\Request;

class PagoController extends Controller
{
    public function index()
    {
        $pagos = Pago::with(['reserva.cliente', 'reserva.estancia.serviciosDetalle'])
            ->orderBy('fecha_pago', 'desc')
            ->paginate(15);
        
        // Calcular información adicional para cada pago
        foreach ($pagos as $pago) {
            $pago->total_reserva = $this->calcularTotalReserva($pago->reserva);
            $pago->total_pagado = $this->calcularTotalPagado($pago->reserva);
            $pago->saldo_pendiente = $pago->total_reserva - $pago->total_pagado;
        }
        
        return view('pagos.index', compact('pagos'));
    }

    private function calcularTotalReserva($reserva)
    {
        $totalHabitacion = $reserva->total_precio ?? 0;
        $serviciosTotal = 0;
        
        if ($reserva->estancia) {
            $serviciosTotal = $reserva->estancia->serviciosDetalle
                ->where('estado', '!=', 'anulado')
                ->sum('subtotal');
        }
        
        return $totalHabitacion + $serviciosTotal;
    }

    private function calcularTotalPagado($reserva)
    {
        return $reserva->pagos()
            ->where('estado', 'completado')
            ->sum('monto') ?? 0;
    }

    public function create(Request $request)
    {
        $reserva_id = $request->get('reserva_id');
        
        // Si viene desde reserva, cargar datos de la reserva
        if ($reserva_id) {
            $reserva = Reserva::with(['pagos', 'estancia.serviciosDetalle', 'cliente', 'habitacion'])->findOrFail($reserva_id);
            
            // Calcular monto pendiente
            $totalPagado = $reserva->pagos->where('estado', 'completado')->sum('monto');
            $serviciosTotal = $reserva->estancia ? $reserva->estancia->serviciosDetalle->where('estado', '!=', 'anulado')->sum('subtotal') : 0;
            $totalGeneral = $reserva->total_precio + $serviciosTotal;
            $saldoPendiente = $totalGeneral - $totalPagado;
            
            return view('pagos.create', compact('reserva', 'saldoPendiente', 'totalGeneral', 'totalPagado'));
        }
        
        // Si viene desde index de pagos, mostrar todas las reservas
        $reservas = Reserva::with('cliente')->whereNotIn('estado', ['cancelada'])->get();
        return view('pagos.create', compact('reservas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'reserva_id' => 'required|exists:reservas,id',
            'monto' => 'required|numeric|min:0.01',
            'metodo_pago' => 'required|in:efectivo,tarjeta,transferencia',
            'numero_transaccion' => 'nullable|string|max:100',
            'comprobante' => 'nullable|string|max:255',
        ]);

        $pago = Pago::create([
            'reserva_id' => $validated['reserva_id'],
            'monto' => $validated['monto'],
            'metodo_pago' => $validated['metodo_pago'],
            'numero_transaccion' => $validated['numero_transaccion'] ?? null,
            'comprobante' => $validated['comprobante'] ?? null,
            'fecha_pago' => now(),
            'estado' => 'completado',
            'usuario_id' => auth()->id(),
        ]);

        // Actualizar estado de reserva si está totalmente pagada
        $reserva = Reserva::with(['pagos', 'estancia.serviciosDetalle'])->find($validated['reserva_id']);
        $totalPagado = $reserva->pagos->where('estado', 'completado')->sum('monto');
        $serviciosTotal = $reserva->estancia ? $reserva->estancia->serviciosDetalle->where('estado', '!=', 'anulado')->sum('subtotal') : 0;
        $totalGeneral = $reserva->total_precio + $serviciosTotal;
        
        if ($totalPagado >= $totalGeneral && !in_array($reserva->estado, ['completada', 'checkout'])) {
            $reserva->estado = 'confirmada';
            $reserva->save();
        }

        return redirect()->route('reservas.show', $validated['reserva_id'])
            ->with('success', 'Pago registrado correctamente');
    }

    public function show(Pago $pago)
    {
        $pago->load(['reserva.cliente', 'reserva.habitacion.tipoHabitacion', 'reserva.estancia.serviciosDetalle', 'usuario']);
        return view('pagos.show', compact('pago'));
    }

    public function edit(Pago $pago)
    {
        // Solo administrador puede editar
        if (auth()->user()->role !== 'administrador') {
            abort(403, 'No tiene permisos para editar pagos');
        }

        $pago->load(['reserva.cliente']);
        return view('pagos.edit', compact('pago'));
    }

    public function update(Request $request, Pago $pago)
    {
        // Solo administrador puede actualizar
        if (auth()->user()->role !== 'administrador') {
            abort(403, 'No tiene permisos para editar pagos');
        }

        $validated = $request->validate([
            'monto' => 'required|numeric|min:0.01',
            'metodo_pago' => 'required|in:efectivo,tarjeta,transferencia',
            'numero_transaccion' => 'nullable|string|max:100',
            'comprobante' => 'nullable|string|max:255',
            'estado' => 'required|in:pendiente,completado,anulado',
            'fecha_pago' => 'required|date',
        ]);

        $pago->update([
            'monto' => $validated['monto'],
            'metodo_pago' => $validated['metodo_pago'],
            'numero_transaccion' => $validated['numero_transaccion'] ?? null,
            'comprobante' => $validated['comprobante'] ?? null,
            'estado' => $validated['estado'],
            'fecha_pago' => $validated['fecha_pago'],
        ]);

        return redirect()->route('pagos.index')
            ->with('success', 'Pago actualizado correctamente');
    }

    public function destroy(Pago $pago)
    {
        $reserva_id = $pago->reserva_id;
        
        // Marcar como anulado en lugar de eliminar
        $pago->update([
            'estado' => 'anulado',
            'anulado_por' => auth()->id(),
            'fecha_anulacion' => now(),
            'motivo_anulacion' => 'Anulado por usuario'
        ]);

        return redirect()->route('reservas.show', $reserva_id)
            ->with('success', 'Pago anulado correctamente');
    }
}
