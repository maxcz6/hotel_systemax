<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Salida extends Model
{
    protected $table = 'salidas';

    protected $fillable = [
        'reserva_id',
        'habitacion_id',
        'cliente_id',
        'fecha_salida_real',
        'monto_total',
        'estado',
        'notas',
        'usuario_id',
    ];

    protected $casts = [
        'fecha_salida_real' => 'datetime',
    ];

    public function reserva()
    {
        return $this->belongsTo(Reserva::class);
    }

    public function habitacion()
    {
        return $this->belongsTo(Habitacion::class);
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class);
    }
}
