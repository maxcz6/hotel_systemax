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
        $servicios = Servicio::where('disponible', 1)->get();
        
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
        
        $servicioDetalle = new ServicioDetalle();
        $servicioDetalle->id_estancia = $validated['id_estancia'];
        $servicioDetalle->id_servicio = $validated['id_servicio'];
        $servicioDetalle->cantidad = $validated['cantidad'];
        $servicioDetalle->precio_unitario = $servicio->precio;
        $servicioDetalle->subtotal = $servicio->precio * $validated['cantidad'];
        $servicioDetalle->save();

        return redirect()->route('reservas.show', $servicioDetalle->estancia->id_reserva)
            ->with('success', 'Servicio agregado correctamente');
    }

    public function edit(ServicioDetalle $servicioDetalle)
    {
        $servicios = Servicio::where('disponible', 1)->get();
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

        return redirect()->route('reservas.show', $servicioDetalle->estancia->id_reserva)
            ->with('success', 'Servicio actualizado correctamente');
    }

    public function destroy(ServicioDetalle $servicioDetalle)
    {
        $reserva_id = $servicioDetalle->estancia->id_reserva;
        $servicioDetalle->delete();

        return redirect()->route('reservas.show', $reserva_id)
            ->with('success', 'Servicio eliminado correctamente');
    }
}
