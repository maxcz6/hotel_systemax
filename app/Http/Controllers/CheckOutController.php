<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use App\Models\Estancia;
use App\Models\Habitacion;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CheckOutController extends Controller
{
    public function create(Reserva $reserva)
    {
        if ($reserva->estado !== 'activa') {
            return redirect()->back()->with('error', 'La reserva no está activa.');
        }

        // Cargar relaciones para mostrar detalles
        $reserva->load('servicioDetalles.servicio', 'pagos', 'habitacion');

        // Calcular totales
        $totalServicios = $reserva->servicioDetalles->sum('total');
        $totalPagado = $reserva->pagos->sum('monto');
        $totalPagar = $reserva->total_precio + $totalServicios;
        $pendiente = $totalPagar - $totalPagado;

        return view('checkout.create', compact('reserva', 'totalServicios', 'totalPagado', 'totalPagar', 'pendiente'));
    }

    public function store(Request $request, Reserva $reserva)
    {
        if ($reserva->estado !== 'activa') {
            return redirect()->back()->with('error', 'La reserva no está activa.');
        }

        // Actualizar Estancia
        $estancia = $reserva->estancia;
        if ($estancia) {
            $estancia->update([
                'check_out_real' => Carbon::now(),
                'estado' => 'finalizada',
            ]);
        }

        // Actualizar Reserva
        $reserva->update(['estado' => 'completada']);

        // Actualizar Habitación a Limpieza
        $habitacion = $reserva->habitacion;
        if ($habitacion) {
            $habitacion->update(['estado' => 'limpieza']);
        }

        return redirect()->route('reservas.index')->with('success', 'Check-out realizado con éxito. Habitación marcada para limpieza.');
    }
}
