<?php

namespace App\Http\Controllers;

use App\Models\Pago;
use App\Models\Reserva;
use Illuminate\Http\Request;

class PagoController extends Controller
{
    public function index()
    {
        $pagos = Pago::with(['reserva.cliente'])
            ->orderBy('fecha', 'desc')
            ->paginate(15);
        
        return view('pagos.index', compact('pagos'));
    }

    public function create(Request $request)
    {
        $reserva_id = $request->get('reserva_id');
        $reserva = Reserva::with(['pagos', 'estancia.serviciosDetalle'])->findOrFail($reserva_id);
        
        // Calculate pending amount
        $totalPagado = $reserva->pagos->sum('monto');
        $serviciosTotal = $reserva->estancia ? $reserva->estancia->serviciosDetalle->sum('subtotal') : 0;
        $totalGeneral = $reserva->precio_total + $serviciosTotal;
        $saldoPendiente = $totalGeneral - $totalPagado;
        
        return view('pagos.create', compact('reserva', 'saldoPendiente'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_reserva' => 'required|exists:reservas,id',
            'monto' => 'required|numeric|min:0.01',
            'metodo_pago' => 'required|in:efectivo,tarjeta,transferencia',
            'referencia' => 'nullable|string|max:100',
        ]);

        $pago = new Pago();
        $pago->id_reserva = $validated['id_reserva'];
        $pago->monto = $validated['monto'];
        $pago->metodo_pago = $validated['metodo_pago'];
        $pago->referencia = $validated['referencia'] ?? null;
        $pago->fecha = now();
        $pago->save();

        // Update reservation status if fully paid
        $reserva = Reserva::with(['pagos', 'estancia.serviciosDetalle'])->find($validated['id_reserva']);
        $totalPagado = $reserva->pagos->sum('monto');
        $serviciosTotal = $reserva->estancia ? $reserva->estancia->serviciosDetalle->sum('subtotal') : 0;
        $totalGeneral = $reserva->precio_total + $serviciosTotal;
        
        if ($totalPagado >= $totalGeneral && $reserva->estado !== 'completada') {
            $reserva->estado = 'pagada';
            $reserva->save();
        }

        return redirect()->route('reservas.show', $validated['id_reserva'])
            ->with('success', 'Pago registrado correctamente');
    }

    public function show(Pago $pago)
    {
        return view('pagos.show', compact('pago'));
    }

    public function destroy(Pago $pago)
    {
        $reserva_id = $pago->id_reserva;
        $pago->delete();

        return redirect()->route('reservas.show', $reserva_id)
            ->with('success', 'Pago eliminado correctamente');
    }
}
