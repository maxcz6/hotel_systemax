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
        $user = auth()->user();
        
        // Dashboard para Administrador
        if ($user->role === 'administrador') {
            $habitacionesDisponibles = Habitacion::where('estado', 'disponible')->count();
            $habitacionesOcupadas = Habitacion::where('estado', 'ocupada')->count();
            $totalUsuarios = \App\Models\User::count();
            $totalClientes = \App\Models\Cliente::count();
            
            $today = Carbon::today();
            $reservasHoy = Reserva::whereDate('fecha_entrada', $today)->count();
            $checkinsPendientes = Reserva::whereDate('fecha_entrada', $today)->where('estado', 'confirmada')->count();
            $checkoutsPendientes = Reserva::whereDate('fecha_salida', $today)->where('estado', 'check-in')->count();
            $ingresosHoy = Pago::whereDate('fecha_pago', $today)->sum('monto');
            $ingresosMes = Pago::whereMonth('fecha_pago', Carbon::now()->month)->sum('monto');
            
            return view('dashboards.administrador', compact(
                'habitacionesDisponibles',
                'habitacionesOcupadas',
                'totalUsuarios',
                'totalClientes',
                'reservasHoy',
                'checkinsPendientes',
                'checkoutsPendientes',
                'ingresosHoy',
                'ingresosMes'
            ));
        }
        
        // Dashboard para Gerente
        if ($user->role === 'gerente') {
            $habitacionesDisponibles = Habitacion::where('estado', 'disponible')->count();
            $habitacionesOcupadas = Habitacion::where('estado', 'ocupada')->count();
            $habitacionesLimpieza = Habitacion::where('estado', 'limpieza')->count();
            $habitacionesMantenimiento = Habitacion::where('estado', 'mantenimiento')->count();
            
            $today = Carbon::today();
            $reservasHoy = Reserva::whereDate('fecha_entrada', $today)->count();
            $checkinsPendientes = Reserva::whereDate('fecha_entrada', $today)->where('estado', 'confirmada')->count();
            $checkoutsPendientes = Reserva::whereDate('fecha_salida', $today)->where('estado', 'check-in')->count();
            $ingresosHoy = Pago::whereDate('fecha_pago', $today)->sum('monto');
            $ingresosMes = Pago::whereMonth('fecha_pago', Carbon::now()->month)->sum('monto');
            
            return view('dashboards.gerente', compact(
                'habitacionesDisponibles',
                'habitacionesOcupadas',
                'habitacionesLimpieza',
                'habitacionesMantenimiento',
                'reservasHoy',
                'checkinsPendientes',
                'checkoutsPendientes',
                'ingresosHoy',
                'ingresosMes'
            ));
        }
        
        // Dashboard para Recepción
        if ($user->role === 'recepcion') {
            $habitacionesDisponibles = Habitacion::where('estado', 'disponible')->count();
            $habitacionesOcupadas = Habitacion::where('estado', 'ocupada')->count();

            $today = Carbon::today();
            $reservasHoy = Reserva::whereDate('fecha_entrada', $today)->count();
            $checkinsPendientes = Reserva::whereDate('fecha_entrada', $today)->where('estado', 'confirmada')->count();
            $checkoutsPendientes = Reserva::whereDate('fecha_salida', $today)->where('estado', 'check-in')->count();
            $ingresosHoy = Pago::whereDate('fecha_pago', $today)->sum('monto');

            return view('dashboards.recepcion', compact(
                'habitacionesDisponibles',
                'habitacionesOcupadas',
                'reservasHoy',
                'checkinsPendientes',
                'checkoutsPendientes',
                'ingresosHoy'
            ));
        }
        
        // Dashboard para Limpieza
        if ($user->role === 'limpieza') {
            $habitacionesLimpieza = Habitacion::where('estado', 'limpieza')->count();
            $habitacionesDisponibles = Habitacion::where('estado', 'disponible')->count();
            $habitacionesOcupadas = Habitacion::where('estado', 'ocupada')->count();
            $totalHabitaciones = Habitacion::count();
            
            return view('dashboards.limpieza', compact(
                'habitacionesLimpieza',
                'habitacionesDisponibles',
                'habitacionesOcupadas',
                'totalHabitaciones'
            ));
        }
        
        // Dashboard para Mantenimiento
        if ($user->role === 'mantenimiento') {
            $habitacionesMantenimiento = Habitacion::where('estado', 'mantenimiento')->count();
            $habitacionesDisponibles = Habitacion::where('estado', 'disponible')->count();
            $habitacionesOcupadas = Habitacion::where('estado', 'ocupada')->count();
            $totalHabitaciones = Habitacion::count();
            
            return view('dashboards.mantenimiento', compact(
                'habitacionesMantenimiento',
                'habitacionesDisponibles',
                'habitacionesOcupadas',
                'totalHabitaciones'
            ));
        }
        
        // Fallback - Dashboard genérico
        return view('dashboard');
    }
}
