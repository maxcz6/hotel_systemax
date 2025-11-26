<?php

namespace App\Http\Controllers;

use App\Models\TipoHabitacion;
use App\Http\Requests\StoreTipoHabitacionRequest;
use App\Http\Requests\UpdateTipoHabitacionRequest;

class TipoHabitacionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tipoHabitaciones = TipoHabitacion::withCount('habitaciones')->paginate(10);
        return view('tipo_habitaciones.index', compact('tipoHabitaciones'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('tipo_habitaciones.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTipoHabitacionRequest $request)
    {
        TipoHabitacion::create($request->validated());
        return redirect()->route('tipo_habitaciones.index')->with('success', 'Tipo de habitación creado con éxito.');
    }

    /**
     * Display the specified resource.
     */
    public function show(TipoHabitacion $tipoHabitacion)
    {
        return view('tipo_habitaciones.show', compact('tipoHabitacion'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TipoHabitacion $tipoHabitacion)
    {
        $tipoHabitacion->load('habitaciones');
        return view('tipo_habitaciones.edit', compact('tipoHabitacion'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTipoHabitacionRequest $request, TipoHabitacion $tipoHabitacion)
    {
        $tipoHabitacion->update($request->validated());
        return redirect()->route('tipo_habitaciones.index')->with('success', 'Tipo de habitación actualizado con éxito.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TipoHabitacion $tipoHabitacion)
    {
        try {
            // Verificar si tiene habitaciones asociadas
            $habitacionesCount = $tipoHabitacion->habitaciones()->count();
            
            if ($habitacionesCount > 0) {
                return redirect()->route('tipo_habitaciones.index')
                    ->with('error', "No se puede eliminar este tipo porque tiene {$habitacionesCount} habitación(es) asociada(s). Primero elimine o reasigne las habitaciones.");
            }
            
            $tipoHabitacion->delete();
            return redirect()->route('tipo_habitaciones.index')
                ->with('success', 'Tipo de habitación eliminado con éxito.');
                
        } catch (\Exception $e) {
            return redirect()->route('tipo_habitaciones.index')
                ->with('error', 'Error al eliminar el tipo de habitación: ' . $e->getMessage());
        }
    }
}
