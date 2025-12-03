<?php

namespace App\Http\Controllers;

use App\Models\Mantenimiento;
use App\Models\Habitacion;
use App\Models\User;
use Illuminate\Http\Request;

class MantenimientoController extends Controller
{
    public function index()
    {
        $mantenimientos = Mantenimiento::with(['habitacion', 'asignadoA'])
            ->orderBy('fecha_inicio', 'desc')
            ->paginate(15);

        return view('mantenimientos.index', compact('mantenimientos'));
    }

    public function create()
    {
        $habitaciones = Habitacion::orderBy('numero')->get();
        $usuarios = User::where('role', '!=', 'cliente')->get();

        return view('mantenimientos.create', compact('habitaciones', 'usuarios'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'habitacion_id' => 'required|exists:habitaciones,id',
            'tipo' => 'required|in:preventivo,correctivo,urgente',
            'descripcion' => 'required|string|max:500',
            'fecha_inicio' => 'required|date',
            'asignado_a' => 'nullable|exists:users,id',
            'notas_tecnicas' => 'nullable|string|max:1000',
            'costo' => 'nullable|numeric|min:0',
        ]);

        $mantenimiento = Mantenimiento::create([
            'habitacion_id' => $validated['habitacion_id'],
            'tipo' => $validated['tipo'],
            'descripcion' => $validated['descripcion'],
            'estado' => 'pendiente',
            'fecha_inicio' => $validated['fecha_inicio'],
            'asignado_a' => $validated['asignado_a'] ?? null,
            'notas_tecnicas' => $validated['notas_tecnicas'] ?? null,
            'costo' => $validated['costo'] ?? null,
        ]);

        // Marcar habitación como en mantenimiento si es urgente
        if ($validated['tipo'] === 'urgente') {
            $mantenimiento->habitacion->update(['estado' => 'mantenimiento']);
        }

        return redirect()->route('mantenimientos.show', $mantenimiento)
            ->with('success', 'Mantenimiento registrado correctamente');
    }

    public function show(Mantenimiento $mantenimiento)
    {
        $mantenimiento->load(['habitacion', 'asignadoA']);
        return view('mantenimientos.show', compact('mantenimiento'));
    }

    public function edit(Mantenimiento $mantenimiento)
    {
        $habitaciones = Habitacion::orderBy('numero')->get();
        $usuarios = User::where('role', '!=', 'cliente')->get();
        $mantenimiento->load('habitacion');

        return view('mantenimientos.edit', compact('mantenimiento', 'habitaciones', 'usuarios'));
    }

    public function update(Request $request, Mantenimiento $mantenimiento)
    {
        $validated = $request->validate([
            'tipo' => 'required|in:preventivo,correctivo,urgente',
            'descripcion' => 'required|string|max:500',
            'estado' => 'required|in:pendiente,en_progreso,completado',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'asignado_a' => 'nullable|exists:users,id',
            'notas_tecnicas' => 'nullable|string|max:1000',
            'costo' => 'nullable|numeric|min:0',
        ]);

        $estadoAnterior = $mantenimiento->estado;

        $mantenimiento->update($validated);

        // Si se marca como completado, liberar la habitación si estaba en mantenimiento
        if ($validated['estado'] === 'completado' && $estadoAnterior !== 'completado') {
            if ($mantenimiento->habitacion->estado === 'mantenimiento') {
                $mantenimiento->habitacion->update(['estado' => 'disponible']);
            }
        }

        return redirect()->route('mantenimientos.show', $mantenimiento)
            ->with('success', 'Mantenimiento actualizado correctamente');
    }

    public function destroy(Mantenimiento $mantenimiento)
    {
        // Solo administrador
        if (auth()->user()->role !== 'administrador') {
            abort(403, 'No tiene permisos para eliminar mantenimientos');
        }

        $mantenimiento->delete();

        return redirect()->route('mantenimientos.index')
            ->with('success', 'Mantenimiento eliminado correctamente');
    }
}
