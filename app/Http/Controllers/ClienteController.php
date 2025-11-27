<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Http\Requests\StoreClienteRequest;
use App\Http\Requests\UpdateClienteRequest;

class ClienteController extends Controller
{
    /**
     * Muestra un listado del recurso.
     */
    public function index()
    {
        $clientes = Cliente::paginate(10);
        return view('clientes.index', compact('clientes'));
    }

    /**
     * Muestra el formulario para crear un nuevo recurso.
     */
    public function create()
    {
        return view('clientes.create');
    }

    /**
     * Almacena un recurso recién creado en el almacenamiento.
     */
    public function store(StoreClienteRequest $request)
    {
        Cliente::create($request->validated());
        return redirect()->route('clientes.index')->with('success', 'Cliente creado con éxito.');
    }

    /**
     * Muestra el recurso especificado.
     */
    public function show(Cliente $cliente)
    {
        return view('clientes.show', compact('cliente'));
    }

    /**
     * Muestra el formulario para editar el recurso especificado.
     */
    public function edit(Cliente $cliente)
    {
        return view('clientes.edit', compact('cliente'));
    }

    /**
     * Actualiza el recurso especificado en el almacenamiento.
     */
    public function update(UpdateClienteRequest $request, Cliente $cliente)
    {
        $cliente->update($request->validated());
        return redirect()->route('clientes.index')->with('success', 'Cliente actualizado con éxito.');
    }

    /**
     * Elimina el recurso especificado del almacenamiento.
     */
    public function destroy(Cliente $cliente)
    {
        try {
            // Verificar si tiene reservas asociadas
            $reservasCount = $cliente->reservas()->count();
            
            if ($reservasCount > 0) {
                return redirect()->route('clientes.index')
                    ->with('error', "No se puede eliminar el cliente {$cliente->nombre} {$cliente->apellido} porque tiene {$reservasCount} reserva(s) asociada(s). Primero elimine o reasigne las reservas.");
            }
            
            $nombreCompleto = $cliente->nombre . ' ' . $cliente->apellido;
            $cliente->delete();
            
            return redirect()->route('clientes.index')
                ->with('success', "Cliente {$nombreCompleto} eliminado con éxito.");
                
        } catch (\Exception $e) {
            return redirect()->route('clientes.index')
                ->with('error', 'Error al eliminar el cliente: ' . $e->getMessage());
        }
    }
}
