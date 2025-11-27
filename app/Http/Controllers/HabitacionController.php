<?php

namespace App\Http\Controllers;

use App\Models\Habitacion;
use App\Models\TipoHabitacion;
use App\Http\Requests\StoreHabitacionRequest;
use App\Http\Requests\UpdateHabitacionRequest;

class HabitacionController extends Controller
{
    /**
     * Muestra un listado del recurso.
     */
    public function index()
    {
        $habitaciones = Habitacion::with('tipoHabitacion')->paginate(10);
        return view('habitaciones.index', compact('habitaciones'));
    }

    /**
     * Muestra el formulario para crear un nuevo recurso.
     */
    public function create()
    {
        // Solo administradores y gerentes pueden crear habitaciones
        if (!in_array(auth()->user()->role, ['administrador', 'gerente'])) {
            abort(403, 'No tiene permisos para crear habitaciones.');
        }
        
        $tipoHabitaciones = TipoHabitacion::all();
        return view('habitaciones.create', compact('tipoHabitaciones'));
    }

    /**
     * Almacena un recurso recién creado en el almacenamiento.
     */
    public function store(StoreHabitacionRequest $request)
    {
        // Solo administradores y gerentes pueden crear habitaciones
        if (!in_array(auth()->user()->role, ['administrador', 'gerente'])) {
            abort(403, 'No tiene permisos para crear habitaciones.');
        }
        
        $data = $request->validated();
        
        // Si el gerente o administrador seleccionó crear un nuevo tipo de habitación
        if ($request->tipo_habitacion_id === 'nuevo' && in_array(auth()->user()->role, ['administrador', 'gerente'])) {
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
     * Muestra el recurso especificado.
     */
    public function show(Habitacion $habitacion)
    {
        return view('habitaciones.show', compact('habitacion'));
    }

    /**
     * Muestra el formulario para editar el recurso especificado.
     */
    public function edit(Habitacion $habitacion)
    {
        // Solo administradores y gerentes pueden editar habitaciones
        if (!in_array(auth()->user()->role, ['administrador', 'gerente'])) {
            abort(403, 'No tiene permisos para editar habitaciones.');
        }
        
        $tipoHabitaciones = TipoHabitacion::all();
        return view('habitaciones.edit', compact('habitacion', 'tipoHabitaciones'));
    }

    /**
     * Actualiza el recurso especificado en el almacenamiento.
     */
    public function update(UpdateHabitacionRequest $request, Habitacion $habitacion)
    {
        // Solo administradores y gerentes pueden actualizar habitaciones
        if (!in_array(auth()->user()->role, ['administrador', 'gerente'])) {
            abort(403, 'No tiene permisos para actualizar habitaciones.');
        }
        
        $habitacion->update($request->validated());
        return redirect()->route('habitaciones.index')->with('success', 'Habitación actualizada con éxito.');
    }

    /**
     * Elimina el recurso especificado del almacenamiento.
     */
    public function destroy(Habitacion $habitacion)
    {
        // Solo administradores y gerentes pueden eliminar habitaciones
        if (!in_array(auth()->user()->role, ['administrador', 'gerente'])) {
            abort(403, 'No tiene permisos para eliminar habitaciones.');
        }
        
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
