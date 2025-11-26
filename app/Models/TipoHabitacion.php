<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoHabitacion extends Model
{
    use HasFactory;

    protected $table = 'tipo_habitaciones';

    protected $fillable = [
        'nombre',
        'descripcion',
        'capacidad',
        'precio_por_noche',
    ];

    public function habitaciones()
    {
        return $this->hasMany(Habitacion::class);
    }
}
