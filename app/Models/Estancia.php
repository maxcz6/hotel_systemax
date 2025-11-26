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

    public function reserva()
    {
        return $this->belongsTo(Reserva::class);
    }
}
