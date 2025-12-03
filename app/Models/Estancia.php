<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estancia extends Model
{
    use HasFactory;

    protected $table = 'estancias';

    protected $fillable = [
        'reserva_id',
        'check_in_real',
        'check_out_real',
        'estado',
    ];

    protected $casts = [
        'check_in_real' => 'datetime',
        'check_out_real' => 'datetime',
    ];

    public function reserva()
    {
        return $this->belongsTo(Reserva::class);
    }

    public function serviciosDetalle()
    {
        return $this->hasMany(ServicioDetalle::class, 'id_estancia', 'id');
    }
}
