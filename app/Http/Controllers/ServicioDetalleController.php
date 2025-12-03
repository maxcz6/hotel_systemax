<?php

namespace App\Http\Controllers;

use App\Models\ServicioDetalle;
use App\Models\Estancia;
use App\Models\Servicio;
use Illuminate\Http\Request;

class ServicioDetalleController extends Controller
{
    public function index()
    {
        $serviciosDetalle = ServicioDetalle::with(['estancia.reserva.cliente', 'servicio'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('servicio_detalle.index', compact('serviciosDetalle'));
    }

    public function create(Request $request)
    {
        $estancia_id = $request->get('estancia_id');
        $estancia = Estancia::findOrFail($estancia_id);
        $servicios = Servicio::all();
        
        return view('servicio_detalle.create', compact('estancia', 'servicios'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_estancia' => 'required|exists:estancias,id',
            'id_servicio' => 'required|exists:servicios,id',
            'cantidad' => 'required|integer|min:1',
        ]);

        $servicio = Servicio::findOrFail($validated['id_servicio']);
        $estancia = Estancia::findOrFail($validated['id_estancia']);
        
        $servicioDetalle = new ServicioDetalle();
        $servicioDetalle->id_estancia = $validated['id_estancia'];
        $servicioDetalle->id_servicio = $validated['id_servicio'];
        $servicioDetalle->cantidad = $validated['cantidad'];
        $servicioDetalle->precio_unitario = $servicio->precio;
        $servicioDetalle->subtotal = $servicio->precio * $validated['cantidad'];
        $servicioDetalle->save();

        return redirect()->route('reservas.show', $estancia->reserva_id)
            ->with('success', 'Servicio agregado correctamente');
    }

    public function edit(ServicioDetalle $servicioDetalle)
    {
        $servicios = Servicio::all();
        return view('servicio_detalle.edit', compact('servicioDetalle', 'servicios'));
    }

    public function update(Request $request, ServicioDetalle $servicioDetalle)
    {
        $validated = $request->validate([
            'id_servicio' => 'required|exists:servicios,id',
            'cantidad' => 'required|integer|min:1',
        ]);

        $servicio = Servicio::findOrFail($validated['id_servicio']);
        
        $servicioDetalle->id_servicio = $validated['id_servicio'];
        $servicioDetalle->cantidad = $validated['cantidad'];
        $servicioDetalle->precio_unitario = $servicio->precio;
        $servicioDetalle->subtotal = $servicio->precio * $validated['cantidad'];
        $servicioDetalle->save();

        return redirect()->route('reservas.show', $servicioDetalle->estancia->reserva_id)
            ->with('success', 'Servicio actualizado correctamente');
    }

    public function destroy(ServicioDetalle $servicioDetalle)
    {
        // Solo el administrador puede eliminar físicamente
        if (auth()->user()->role !== 'administrador') {
            return redirect()->back()->with('error', 'Solo el administrador puede eliminar registros permanentemente. Utilice la opción de anular.');
        }

        $reserva_id = $servicioDetalle->estancia->id_reserva;
        $servicioDetalle->delete();

        return redirect()->route('reservas.show', $reserva_id)
            ->with('success', 'Servicio eliminado permanentemente.');
    }

    public function anular(Request $request, ServicioDetalle $servicioDetalle)
    {
        // Validar permisos (Admin, Gerente, Recepcion)
        if (!in_array(auth()->user()->role, ['administrador', 'gerente', 'recepcion'])) {
            abort(403, 'No tiene permisos para anular servicios.');
        }

        $request->validate([
            'motivo_anulacion' => 'required|string|max:255',
        ]);

        // Obtener la estancia antes de actualizar
        $estancia = $servicioDetalle->estancia;

        $servicioDetalle->update([
            'estado' => 'anulado',
            'anulado_por' => auth()->id(),
            'fecha_anulacion' => now(),
            'motivo_anulacion' => $request->motivo_anulacion,
        ]);

        return redirect()->route('reservas.show', $estancia->reserva_id)
            ->with('success', 'Servicio anulado correctamente.');
    }
}
