<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Docente extends Model
{
    public $fillable = [
        'categoria',
        'pnf_id',
        'persona_id',
        'horas_dedicacion',
    ];

    // Relación con la tabla condicion contrato
    public function condicionContrato()
    {
        return $this->hasOne(CondicionContrato::class);
    }

    // Relación con la tabla Personas
    public function persona()
    {
        return $this->belongsTo(Persona::class);
    }

    // Relación con la tabla unidad curriculares
    public function unidades_curriculares()
    {
        return $this->belongsToMany(
            UnidadCurricular::class,
            'docente_unidad_curricular',
            'docente_id',
            'unidad_curricular_id'
        );
    }

    // Relación con la tabla PNF
    public function pnf()
    {
        return $this->belongsTo(Pnf::class);
    }

    // Relación con la tabla de Coordinador
    public function coordinador()
    {
        return $this->hasOne(Coordinador::class);
    }

    /*
     * Coordinación activa actual (si existe)
     */
    public function coordinacionActiva()
    {
        return $this->hasOne(Coordinador::class)
                    ->where('activo', true)
                    ->whereNull('fecha_fin');
    }
}
