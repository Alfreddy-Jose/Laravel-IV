<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReporteEstado extends Model
{
    use HasFactory;

    protected $table = 'reporte_estados';

    protected $fillable = [
        'bien_id',
        'lapso_academico_id',
        'estado_operativo',
        'fecha',
        'observaciones',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    /**
     * Relación con el bien.
     */
    public function bien()
    {
        return $this->belongsTo(Bien::class, 'bien_id');
    }

    /**
     * Relación con el lapso académico.
     */
    public function lapsoAcademico()
    {
        return $this->belongsTo(LapsoAcademico::class, 'lapso_academico_id');
    }
}
