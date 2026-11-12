<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $categoria
 * @property int $persona_id
 * @property int $pnf_id
 * @property int $horas_dedicacion
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read CondicionContrato|null $condicionContrato
 * @property-read Coordinador|null $coordinacionActiva
 * @property-read Coordinador|null $coordinador
 * @property-read Persona $persona
 * @property-read Pnf $pnf
 * @property-read Collection<int, UnidadCurricular> $unidades_curriculares
 * @property-read int|null $unidades_curriculares_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Docente newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Docente newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Docente query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Docente whereCategoria($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Docente whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Docente whereHorasDedicacion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Docente whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Docente wherePersonaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Docente wherePnfId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Docente whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Docente extends Model
{
    use Auditable;

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

    /**
     * Nombre completo del docente a partir de su persona.
     * Usado por la bitácora para resolver `persona_id` a un texto legible.
     */
    public function getNombreCompletoAttribute(): string
    {
        return trim(
            ($this->persona->nombre ?? '').' '.($this->persona->apellido ?? '')
        );
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
