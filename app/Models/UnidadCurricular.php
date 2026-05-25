<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class UnidadCurricular extends Model
{
    public $fillable = [
        "nombre",
        "descripcion",
        "unidad_credito",
        "hora_teorica",
        "hora_practica",
        "hora_total_est",
        "periodo",
    ];


    protected static function booted()
    {
        // Esto se ejecuta justo antes de guardar (ya sea creación o edición)
        static::saving(function ($uc) {
            // Lógica de inferencia: Si el nombre contiene "proyecto"
            if (Str::contains(Str::lower($uc->nombre), 'proyecto')) {
                $uc->es_proyecto = true;
            }
        });
    }

    // Relación con la tablla trimestres
    public function trimestres()
    {
        return $this->belongsToMany(Trimestre::class);
    }

    // Relación muchos a muchos con docentes
    public function docentes()
    {
        return $this->belongsToMany(Docente::class);
    }
}
