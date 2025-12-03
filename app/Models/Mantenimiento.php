<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mantenimiento extends Model
{
    protected $table = 'mantenimientos';

    protected $fillable = [
        'habitacion_id',
        'tipo',
        'descripcion',
        'estado',
        'fecha_inicio',
        'fecha_fin',
        'asignado_a',
        'notas_tecnicas',
        'costo',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
    ];

    public function habitacion()
    {
        return $this->belongsTo(Habitacion::class);
    }

    public function asignadoA()
    {
        return $this->belongsTo(User::class, 'asignado_a');
    }
}
