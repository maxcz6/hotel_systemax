<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use App\Models\Cliente;
use App\Models\Habitacion;
use App\Http\Requests\StoreReservaRequest;
use App\Http\Requests\UpdateReservaRequest;
use Carbon\Carbon;

class ReservaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $reservas = Reserva::with(['cliente', 'habitacion'])->paginate(10);
        return view('reservas.index', compact('reservas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $clientes = Cliente::all();
        $habitaciones = Habitacion::where('estado', 'disponible')->get();
        return view('reservas.create', compact('clientes', 'habitaciones'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreReservaRequest $request)
    {
        $validatedData = $request->validated();

        $fechaEntrada = Carbon::parse($validatedData['fecha_entrada']);
        $fechaSalida = Carbon::parse($validatedData['fecha_salida']);
        $noches = $fechaSalida->diffInDays($fechaEntrada);

        $habitacion = Habitacion::findOrFail($validatedData['habitacion_id']);
        $totalPrecio = $noches * $habitacion->precio_por_noche;

        $validatedData['total_precio'] = $totalPrecio;

        Reserva::create($validatedData);

        return redirect()->route('reservas.index')->with('success', 'Reserva creada con éxito.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Reserva $reserva)
    {
        return view('reservas.show', compact('reserva'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Reserva $reserva)
    {
        $clientes = Cliente::all();
        $habitaciones = Habitacion::all(); // Or more complex logic to show available rooms
        return view('reservas.edit', compact('reserva', 'clientes', 'habitaciones'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateReservaRequest $request, Reserva $reserva)
    {
        $validatedData = $request->validated();

        $fechaEntrada = Carbon::parse($validatedData['fecha_entrada']);
        $fechaSalida = Carbon::parse($validatedData['fecha_salida']);
        $noches = $fechaSalida->diffInDays($fechaEntrada);

        $habitacion = Habitacion::findOrFail($validatedData['habitacion_id']);
        $totalPrecio = $noches * $habitacion->precio_por_noche;

        $validatedData['total_precio'] = $totalPrecio;

        $reserva->update($validatedData);

        return redirect()->route('reservas.index')->with('success', 'Reserva actualizada con éxito.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Reserva $reserva)
    {
        $reserva->delete();
        return redirect()->route('reservas.index')->with('success', 'Reserva eliminada con éxito.');
    }
}
