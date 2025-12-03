<?php

namespace App\Http\Controllers;

use App\Models\TipoHabitacion;
use App\Http\Requests\StoreTipoHabitacionRequest;
use App\Http\Requests\UpdateTipoHabitacionRequest;

class TipoHabitacionController extends Controller
{
    /**
     * Muestra un listado del recurso.
     */
    public function index()
    {
        $tipoHabitaciones = TipoHabitacion::withCount('habitaciones')->paginate(10);
        return view('tipo_habitaciones.index', compact('tipoHabitaciones'));
    }

    /**
     * Muestra el formulario para crear un nuevo recurso.
     */
    public function create()
    {
        return view('tipo_habitaciones.create');
    }

    /**
     * Almacena un recurso recién creado en el almacenamiento.
     */
    public function store(StoreTipoHabitacionRequest $request)
    {
        TipoHabitacion::create($request->validated());
        return redirect()->route('tipo_habitaciones.index')->with('success', 'Tipo de habitación creado con éxito.');
    }

    /**
     * Muestra el recurso especificado.
     */
    public function show(TipoHabitacion $tipoHabitacion)
    {
        return view('tipo_habitaciones.show', compact('tipoHabitacion'));
    }

    /**
     * Muestra el formulario para editar el recurso especificado.
     */
    public function edit(TipoHabitacion $tipoHabitacion)
    {
        $tipoHabitacion->load('habitaciones');
        return view('tipo_habitaciones.edit', compact('tipoHabitacion'));
    }

    /**
     * Actualiza el recurso especificado en el almacenamiento.
     */
    public function update(UpdateTipoHabitacionRequest $request, TipoHabitacion $tipoHabitacion)
    {
        $tipoHabitacion->update($request->validated());
        return redirect()->route('tipo_habitaciones.index')->with('success', 'Tipo de habitación actualizado con éxito.');
    }

    /**
     * Elimina el recurso especificado del almacenamiento.
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
