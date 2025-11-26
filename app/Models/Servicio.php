<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Servicio extends Model
{
    use HasFactory;

    protected $table = 'servicios';

    protected $fillable = [
        'nombre',
        'descripcion',
        'precio',
    ];

    public function reservas()
    {
        return $this->belongsToMany(Reserva::class, 'servicio_detalles')
                    ->withPivot('cantidad', 'precio_unitario', 'total');
    }
}
