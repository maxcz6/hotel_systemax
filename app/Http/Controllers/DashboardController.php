<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Habitacion;
use App\Models\Reserva;
use App\Models\Pago;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $habitacionesDisponibles = Habitacion::where('estado', 'disponible')->count();
        $habitacionesOcupadas = Habitacion::where('estado', 'ocupada')->count();

        $today = Carbon::today();
        $reservasHoy = Reserva::whereDate('fecha_entrada', $today)->count();
        $checkinsPendientes = Reserva::whereDate('fecha_entrada', $today)->where('estado', 'confirmada')->count();
        $checkoutsPendientes = Reserva::whereDate('fecha_salida', $today)->where('estado', 'check-in')->count();
        $ingresosHoy = Pago::whereDate('fecha_pago', $today)->sum('monto');

        return view('dashboard', compact(
            'habitacionesDisponibles',
            'habitacionesOcupadas',
            'reservasHoy',
            'checkinsPendientes',
            'checkoutsPendientes',
            'ingresosHoy'
        ));
    }
}
