<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstadisticaInscripcion extends Model
{
    protected $table = 'estadistica_inscripciones';

    protected $fillable = [
        'lapso_id',
        'unidad_curricular_id',
        'seccion',
        'cantidad_estudiantes',
    ];

    public function unidadCurricular()
    {
        return $this->belongsTo(UnidadCurricular::class, 'unidad_curricular_id');
    }

    public function lapsoAcademico()
    {
        return $this->belongsTo(LapsoAcademico::class, 'lapso_academico_id');
    }
}
