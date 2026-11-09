<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bitacora extends Model
{
    protected $table = 'bitacoras';

    protected $fillable = [
        'user_id',
        'accion',
        'tabla',
        'registro_id',
        'datos_anteriores',
        'datos_nuevos',
    ];

    // Convierte automáticamente los campos JSON a Array en PHP
    protected $casts = [
        'datos_anteriores' => 'array',
        'datos_nuevos'     => 'array',
    ];

    // Relación con usuario (para ver quién hizo el cambio)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
