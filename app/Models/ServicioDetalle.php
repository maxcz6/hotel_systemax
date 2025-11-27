<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicioDetalle extends Model
{
    use HasFactory;

    protected $table = 'servicio_detalles';

    protected $fillable = [
        'id_estancia',
        'id_servicio',
        'cantidad',
        'precio_unitario',
        'subtotal',
        'estado',
        'anulado_por',
        'fecha_anulacion',
        'motivo_anulacion',
    ];

    public function estancia()
    {
        return $this->belongsTo(Estancia::class, 'id_estancia');
    }

    public function servicio()
    {
        return $this->belongsTo(Servicio::class, 'id_servicio');
    }

    public function anuladoPor()
    {
        return $this->belongsTo(User::class, 'anulado_por');
    }
}
