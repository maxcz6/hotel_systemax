<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Habitacion;
use App\Models\Reserva;
use App\Models\Pago;
use App\Models\Cliente;
use App\Models\TipoHabitacion;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Datos básicos de habitaciones
        $habitacionesDisponibles = Habitacion::where('estado', 'disponible')->count();
        $habitacionesOcupadas = Habitacion::where('estado', 'ocupada')->count();
        $habitacionesTotales = Habitacion::count();

        // Datos de hoy
        $today = Carbon::today();
        $reservasHoy = Reserva::whereDate('fecha_entrada', $today)->count();
        $checkinsPendientes = Reserva::whereDate('fecha_entrada', $today)->where('estado', 'confirmada')->count();
        $checkoutsPendientes = Reserva::whereDate('fecha_salida', $today)->where('estado', 'checkin')->count();
        $ingresosHoy = Pago::whereDate('fecha_pago', $today)->where('estado', 'completado')->sum('monto');

        // Datos de este mes
        $inicioMes = Carbon::now()->startOfMonth();
        $finMes = Carbon::now()->endOfMonth();
        $ingresosMes = Pago::whereBetween('fecha_pago', [$inicioMes, $finMes])->where('estado', 'completado')->sum('monto');
        $reservasMes = Reserva::whereBetween('created_at', [$inicioMes, $finMes])->count();

        // Ocupación por tipo de habitación
        $ocupacionPorTipo = TipoHabitacion::withCount([
            'habitaciones',
            'habitaciones as ocupadas' => function ($q) {
                $q->where('estado', 'ocupada');
            }
        ])->get();

        // Reservas próximas (próximos 7 días)
        $reservasProximas = Reserva::whereBetween('fecha_entrada', [Carbon::today(), Carbon::today()->addDays(7)])
            ->with(['cliente', 'habitacion'])
            ->orderBy('fecha_entrada')
            ->limit(5)
            ->get();

        // Pagos pendientes
        $pagosPendientes = Reserva::where('estado', '!=', 'cancelada')
            ->with(['pagos', 'estancia.serviciosDetalle'])
            ->get()
            ->filter(function ($reserva) {
                $totalHabitacion = $reserva->total_precio ?? 0;
                $servicios = $reserva->estancia ? $reserva->estancia->serviciosDetalle->where('estado', '!=', 'anulado')->sum('subtotal') : 0;
                $totalReserva = $totalHabitacion + $servicios;
                $totalPagado = $reserva->pagos()->where('estado', 'completado')->sum('monto');
                return $totalPagado < $totalReserva;
            })
            ->count();

        // Tasa de ocupación
        $tasaOcupacion = $habitacionesTotales > 0 ? round(($habitacionesOcupadas / $habitacionesTotales) * 100, 2) : 0;

        // Clientes activos (con reservas en progreso)
        $clientesActivos = Cliente::whereHas('reservas', function ($query) {
            $query->whereIn('estado', ['confirmada', 'checkin']);
        })->count();

        return view('dashboard', compact(
            'habitacionesDisponibles',
            'habitacionesOcupadas',
            'habitacionesTotales',
            'tasaOcupacion',
            'reservasHoy',
            'checkinsPendientes',
            'checkoutsPendientes',
            'ingresosHoy',
            'ingresosMes',
            'reservasMes',
            'ocupacionPorTipo',
            'reservasProximas',
            'pagosPendientes',
            'clientesActivos'
        ));
    }
}
