<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reserva extends Model
{
    use HasFactory;

    protected $table = 'reservas';

    protected $fillable = [
        'cliente_id',
        'habitacion_id',
        'fecha_entrada',
        'fecha_salida',
        'total_precio',
        'estado',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function habitacion()
    {
        return $this->belongsTo(Habitacion::class);
    }

    public function estancia()
    {
        return $this->hasOne(Estancia::class);
    }

    public function servicioDetalles()
    {
        return $this->hasMany(ServicioDetalle::class);
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class);
    }

    public function servicios()
    {
        return $this->belongsToMany(Servicio::class, 'servicio_detalles')
                    ->withPivot('cantidad', 'precio_unitario', 'total');
    }
}
