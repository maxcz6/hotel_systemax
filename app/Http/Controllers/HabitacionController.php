<?php

namespace App\Http\Controllers;

use App\Models\Habitacion;
use App\Models\TipoHabitacion;
use App\Http\Requests\StoreHabitacionRequest;
use App\Http\Requests\UpdateHabitacionRequest;

class HabitacionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $habitaciones = Habitacion::with('tipoHabitacion')->paginate(10);
        return view('habitaciones.index', compact('habitaciones'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $tipoHabitaciones = TipoHabitacion::all();
        return view('habitaciones.create', compact('tipoHabitaciones'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreHabitacionRequest $request)
    {
        $data = $request->validated();
        
        // Si el gerente seleccionó crear un nuevo tipo de habitación
        if ($request->tipo_habitacion_id === 'nuevo' && auth()->user()->role === 'gerente') {
            // Validar los campos del nuevo tipo
            $request->validate([
                'nuevo_tipo_nombre' => 'required|string|max:100',
                'nuevo_tipo_descripcion' => 'nullable|string',
                'nuevo_tipo_capacidad' => 'nullable|integer|min:1',
                'nuevo_tipo_precio' => 'required|numeric|min:0',
            ]);
            
            // Crear el nuevo tipo de habitación
            $nuevoTipo = TipoHabitacion::create([
                'nombre' => $request->nuevo_tipo_nombre,
                'descripcion' => $request->nuevo_tipo_descripcion,
                'capacidad' => $request->nuevo_tipo_capacidad ?? 2,
                'precio_por_noche' => $request->nuevo_tipo_precio,
            ]);
            
            // Asignar el ID del nuevo tipo creado
            $data['tipo_habitacion_id'] = $nuevoTipo->id;
        }
        
        Habitacion::create($data);
        return redirect()->route('habitaciones.index')->with('success', 'Habitación creada con éxito.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Habitacion $habitacion)
    {
        return view('habitaciones.show', compact('habitacion'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Habitacion $habitacion)
    {
        $tipoHabitaciones = TipoHabitacion::all();
        return view('habitaciones.edit', compact('habitacion', 'tipoHabitaciones'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateHabitacionRequest $request, Habitacion $habitacion)
    {
        $habitacion->update($request->validated());
        return redirect()->route('habitaciones.index')->with('success', 'Habitación actualizada con éxito.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Habitacion $habitacion)
    {
        try {
            // Verificar si tiene reservas asociadas
            $reservasCount = $habitacion->reservas()->count();
            
            if ($reservasCount > 0) {
                return redirect()->route('habitaciones.index')
                    ->with('error', "No se puede eliminar la habitación #{$habitacion->numero} porque tiene {$reservasCount} reserva(s) asociada(s). Primero elimine o reasigne las reservas.");
            }
            
            $numeroHabitacion = $habitacion->numero;
            $habitacion->delete();
            
            return redirect()->route('habitaciones.index')
                ->with('success', "Habitación #{$numeroHabitacion} eliminada con éxito.");
                
        } catch (\Exception $e) {
            return redirect()->route('habitaciones.index')
                ->with('error', 'Error al eliminar la habitación: ' . $e->getMessage());
        }
    }
}
