<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistroPer extends Model
{
    use HasFactory;

    protected $table = 'registro_pers';

    protected $fillable = [
        'estudiante_id',
        'unidad_curricular_id',
        'docente_id',
        'lapso_academico_id',
        'calificacion',
        // 'estado_evaluacion',
        // 'acta_entregada',
        'observaciones',
    ];

    // --- RELACIONES ELOQUENT (Lado N -> 1) ---

    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class, 'estudiante_id');
    }

    public function unidadCurricular()
    {
        return $this->belongsTo(UnidadCurricular::class, 'unidad_curricular_id');
    }

    public function docente()
    {
        return $this->belongsTo(Docente::class, 'docente_id');
    }

    public function lapsoAcademico()
    {
        return $this->belongsTo(LapsoAcademico::class, 'lapso_academico_id');
    }
}
