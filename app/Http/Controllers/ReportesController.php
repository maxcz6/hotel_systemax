<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use App\Models\Habitacion;
use App\Models\Pago;
use App\Models\ServicioDetalle;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportesController extends Controller
{
    public function index()
    {
        return view('reportes.index');
    }

    public function ingresos(Request $request)
    {
        $fechaInicio = $request->get('fecha_inicio', Carbon::now()->startOfMonth());
        $fechaFin = $request->get('fecha_fin', Carbon::now()->endOfMonth());

        $ingresos = Pago::whereBetween('fecha_pago', [$fechaInicio, $fechaFin])
            ->selectRaw('DATE(fecha_pago) as fecha, SUM(monto) as total, metodo_pago')
            ->groupBy('fecha', 'metodo_pago')
            ->orderBy('fecha', 'desc')
            ->get();

        $totalIngresos = Pago::whereBetween('fecha_pago', [$fechaInicio, $fechaFin])->sum('monto');

        $ingresosPorMetodo = Pago::whereBetween('fecha_pago', [$fechaInicio, $fechaFin])
            ->selectRaw('metodo_pago, SUM(monto) as total')
            ->groupBy('metodo_pago')
            ->get();

        return view('reportes.ingresos', compact('ingresos', 'totalIngresos', 'ingresosPorMetodo', 'fechaInicio', 'fechaFin'));
    }

    public function ocupacion(Request $request)
    {
        $fechaInicio = $request->get('fecha_inicio', Carbon::now()->startOfMonth());
        $fechaFin = $request->get('fecha_fin', Carbon::now()->endOfMonth());

        $totalHabitaciones = Habitacion::count();
        
        // Occupancy by day
        $ocupacionDiaria = Reserva::where('estado', 'confirmada')
            ->orWhere('estado', 'ocupada')
            ->whereBetween('fecha_entrada', [$fechaInicio, $fechaFin])
            ->selectRaw('DATE(fecha_entrada) as fecha, COUNT(*) as habitaciones_ocupadas')
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get();

        // Most occupied rooms
        $habitacionesMasOcupadas = Habitacion::withCount(['reservas' => function ($query) use ($fechaInicio, $fechaFin) {
                $query->whereBetween('fecha_entrada', [$fechaInicio, $fechaFin]);
            }])
            ->orderBy('reservas_count', 'desc')
            ->limit(10)
            ->get();

        // Calculate average occupancy percentage
        $diasEnPeriodo = Carbon::parse($fechaInicio)->diffInDays(Carbon::parse($fechaFin)) + 1;
        $totalDisponible = $totalHabitaciones * $diasEnPeriodo;
        $totalOcupadas = Reserva::whereBetween('fecha_entrada', [$fechaInicio, $fechaFin])
            ->where(function($q) {
                $q->where('estado', 'confirmada')
                  ->orWhere('estado', 'ocupada')
                  ->orWhere('estado', 'completada');
            })
            ->sum(DB::raw('DATEDIFF(fecha_salida, fecha_entrada)'));
        
        $porcentajeOcupacion = $totalDisponible > 0 ? ($totalOcupadas / $totalDisponible) * 100 : 0;

        return view('reportes.ocupacion', compact(
            'ocupacionDiaria',
            'habitacionesMasOcupadas',
            'porcentajeOcupacion',
            'totalHabitaciones',
            'fechaInicio',
            'fechaFin'
        ));
    }

    public function servicios(Request $request)
    {
        $fechaInicio = $request->get('fecha_inicio', Carbon::now()->startOfMonth());
        $fechaFin = $request->get('fecha_fin', Carbon::now()->endOfMonth());

        $serviciosMasUsados = ServicioDetalle::with('servicio')
            ->whereHas('reserva', function ($query) use ($fechaInicio, $fechaFin) {
                $query->whereBetween('fecha_entrada', [$fechaInicio, $fechaFin]);
            })
            ->selectRaw('servicio_id, SUM(cantidad) as total_cantidad, SUM(total) as total_ingresos')
            ->groupBy('servicio_id')
            ->orderBy('total_cantidad', 'desc')
            ->get();

        $totalIngresosServicios = $serviciosMasUsados->sum('total_ingresos');

        return view('reportes.servicios', compact(
            'serviciosMasUsados',
            'totalIngresosServicios',
            'fechaInicio',
            'fechaFin'
        ));
    }

    public function general(Request $request)
    {
        $fechaInicio = $request->get('fecha_inicio', Carbon::now()->startOfMonth());
        $fechaFin = $request->get('fecha_fin', Carbon::now()->endOfMonth());

        // Total stats
        $totalReservas = Reserva::whereBetween('fecha_entrada', [$fechaInicio, $fechaFin])->count();
        $totalIngresos = Pago::whereBetween('fecha_pago', [$fechaInicio, $fechaFin])->sum('monto');
        $totalHabitaciones = Habitacion::count();
        
        // Status breakdown
        $reservasPorEstado = Reserva::whereBetween('fecha_entrada', [$fechaInicio, $fechaFin])
            ->selectRaw('estado, COUNT(*) as total')
            ->groupBy('estado')
            ->get();

        return view('reportes.general', compact(
            'totalReservas',
            'totalIngresos',
            'totalHabitaciones',
            'reservasPorEstado',
            'fechaInicio',
            'fechaFin'
        ));
    }
}
