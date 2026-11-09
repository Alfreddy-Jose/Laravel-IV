<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\Auditable;

/**
 * @property int $id
 * @property int $ano
 * @property \Illuminate\Support\Carbon $fecha_inicio
 * @property \Illuminate\Support\Carbon $fecha_fin
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $estado_real
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LapsoAcademico> $lapsos
 * @property-read int|null $lapsos_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AnoAcademico newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AnoAcademico newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AnoAcademico query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AnoAcademico whereAno($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AnoAcademico whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AnoAcademico whereFechaFin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AnoAcademico whereFechaInicio($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AnoAcademico whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AnoAcademico whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AnoAcademico whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class AnoAcademico extends Model
{
    use Auditable;

    protected $fillable = ['ano', 'fecha_inicio', 'fecha_fin', 'status'];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
    ];

    // Adjuntar automáticamente el estado calculado al convertir a JSON
    protected $appends = ['estado_real'];

    /**
     * ACCESOR: Evalúa el estado real (voluntad del usuario > tiempo)
     */
    public function getEstadoRealAttribute(): ?string
    {
        if ($this->status !== 'cronograma') {
            return $this->status;
        }

        $hoy = Carbon::today();

        if ($hoy->greaterThan($this->fecha_fin)) {
            return 'cerrado';
        }

        // Usando between estándar de Carbon
        if ($hoy->between($this->fecha_inicio, $this->fecha_fin)) {
            return 'activo';
        }

        return 'planificacion';
    }

    // relacion con los lapsos
    public function lapsos(): HasMany
    {
        return $this->hasMany(LapsoAcademico::class, 'ano_academico_id');
    }
}
