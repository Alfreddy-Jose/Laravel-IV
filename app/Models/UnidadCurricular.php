<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Traits\Auditable;

/**
 * @property int $id
 * @property string $nombre
 * @property string|null $descripcion
 * @property bool $es_proyecto
 * @property int $unidad_credito
 * @property int|null $hora_teorica
 * @property int|null $hora_practica
 * @property int $hora_total_est
 * @property string $periodo
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Docente> $docentes
 * @property-read int|null $docentes_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Trimestre> $trimestres
 * @property-read int|null $trimestres_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UnidadCurricular newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UnidadCurricular newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UnidadCurricular query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UnidadCurricular whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UnidadCurricular whereDescripcion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UnidadCurricular whereEsProyecto($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UnidadCurricular whereHoraPractica($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UnidadCurricular whereHoraTeorica($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UnidadCurricular whereHoraTotalEst($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UnidadCurricular whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UnidadCurricular whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UnidadCurricular wherePeriodo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UnidadCurricular whereUnidadCredito($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UnidadCurricular whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class UnidadCurricular extends Model
{
    use Auditable;

    public $fillable = [
        "nombre",
        "codigo",
        "descripcion",
        "unidad_credito",
        "hora_teorica",
        "hora_practica",
        "hora_total_est",
        "periodo",
        "pnf_id",
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

    public function registrosPer()
    {
        return $this->hasMany(RegistroPer::class, 'unidad_curricular_id');
    }
}
