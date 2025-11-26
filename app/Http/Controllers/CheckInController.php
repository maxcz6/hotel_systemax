<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use App\Models\Estancia;
use App\Models\Habitacion;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CheckInController extends Controller
{
    public function create(Reserva $reserva)
    {
        if ($reserva->estado !== 'confirmada' && $reserva->estado !== 'pendiente') {
            return redirect()->back()->with('error', 'La reserva no está en estado válido para Check-in.');
        }

        return view('checkin.create', compact('reserva'));
    }

    public function store(Request $request, Reserva $reserva)
    {
        if ($reserva->estado !== 'confirmada' && $reserva->estado !== 'pendiente') {
            return redirect()->back()->with('error', 'La reserva no está en estado válido para Check-in.');
        }

        // Crear Estancia
        Estancia::create([
            'reserva_id' => $reserva->id,
            'check_in_real' => Carbon::now(),
            'estado' => 'activa',
        ]);

        // Actualizar Reserva
        $reserva->update(['estado' => 'activa']);

        // Actualizar Habitación
        $habitacion = $reserva->habitacion;
        if ($habitacion) {
            $habitacion->update(['estado' => 'ocupada']);
        }

        return redirect()->route('reservas.index')->with('success', 'Check-in realizado con éxito.');
    }
}
