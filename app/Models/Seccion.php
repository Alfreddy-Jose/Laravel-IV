<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Traits\Auditable;

/**
 * @property int $id
 * @property int $pnf_id
 * @property int $matricula_id
 * @property int $trayecto_id
 * @property int $sede_id
 * @property int $lapso_id
 * @property int $numero_seccion
 * @property string $nombre
 * @property string $tipo_seccion
 * @property int $cupo_maximo
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Estudiante> $estudiantes
 * @property-read int|null $estudiantes_count
 * @property-read \App\Models\LapsoAcademico $lapso
 * @property-read \App\Models\Matricula $matricula
 * @property-read \App\Models\Pnf $pnf
 * @property-read \App\Models\Sede $sede
 * @property-read \App\Models\Trayecto $trayecto
 * @method static Builder<static>|Seccion dePnf($pnfId = null)
 * @method static Builder<static>|Seccion newModelQuery()
 * @method static Builder<static>|Seccion newQuery()
 * @method static Builder<static>|Seccion query()
 * @method static Builder<static>|Seccion whereCreatedAt($value)
 * @method static Builder<static>|Seccion whereCupoMaximo($value)
 * @method static Builder<static>|Seccion whereId($value)
 * @method static Builder<static>|Seccion whereLapsoId($value)
 * @method static Builder<static>|Seccion whereMatriculaId($value)
 * @method static Builder<static>|Seccion whereNombre($value)
 * @method static Builder<static>|Seccion whereNumeroSeccion($value)
 * @method static Builder<static>|Seccion wherePnfId($value)
 * @method static Builder<static>|Seccion whereSedeId($value)
 * @method static Builder<static>|Seccion whereTipoSeccion($value)
 * @method static Builder<static>|Seccion whereTrayectoId($value)
 * @method static Builder<static>|Seccion whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Seccion extends Model
{
    use Auditable;

    protected $fillable = [
        'pnf_id',
        'matricula_id',
        'trayecto_id',
        'sede_id',
        'numero_seccion',
        'lapso_id'
    ];


    public function trayecto()
    {
        return $this->belongsTo(Trayecto::class);
    }

    public function pnf()
    {
        return $this->belongsTo(Pnf::class);
    }

    // Relación con el modelo LapsoAcademico
    public function lapso()
    {
        return $this->belongsTo(LapsoAcademico::class, 'lapso_id');
    }

    // Relación con el modelo Sede
    public function sede()
    {
        return $this->belongsTo(Sede::class);
    }
    // Relación con el modelo Matricula
    public function matricula()
    {
        return $this->belongsTo(Matricula::class);
    }

    /**
         * Relación Muchos a Muchos con Estudiantes.
         * Permite conocer qué alumnos pertenecen o pertenecieron a este grupo académico.
         */
    public function estudiantes()
    {
        return $this->belongsToMany(Estudiante::class, 'estudiantes_seccion', 'seccion_id', 'estudiante_id')
                    ->withPivot('estatus')
                    ->withTimestamps();
    }

    /**
     * Scope para filtrar secciones por PNF de forma opcional.
     */
    public function scopeDePnf(Builder $query, $pnfId = null)
    {
        // 'when' evalúa si $pnfId tiene un valor. Si existe, aplica el 'where'.
        return $query->when($pnfId, function ($q) use ($pnfId) {
            return $q->where('pnf_id', (int)$pnfId);
        });
    }

    /**
     * Relación Uno a Muchos con Voceros.
     * Permite obtener directamente la pareja de voceros (Principal y Suplente) del grupo.
     */
    /*    public function voceros()
        {
            return $this->hasMany(Vocero::class, 'seccion_id');
        }*/
}
