<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicioDetalle extends Model
{
    use HasFactory;

    protected $table = 'servicio_detalles';

    protected $fillable = [
        'reserva_id',
        'servicio_id',
        'cantidad',
        'precio_unitario',
        'total',
    ];

    public function reserva()
    {
        return $this->belongsTo(Reserva::class);
    }

    public function servicio()
    {
        return $this->belongsTo(Servicio::class);
    }
}
