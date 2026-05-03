<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LapsoAcademico extends Model
{
    protected $fillable = [
        'nombre_lapso',
        'ano',
        'tipo_lapso_id',
        'fecha_inicio',
        'fecha_fin',
        'status'
    ];

    public function tipoLapso()
    {
        return $this->belongsTo(TipoLapso::class);
    }
}
