<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use App\Traits\Auditable;

/**
 * @property int $id
 * @property string $nombre_lapso
 * @property \Illuminate\Support\Carbon|null $fecha_inicio
 * @property \Illuminate\Support\Carbon|null $fecha_fin
 * @property string $status
 * @property int $ano_academico_id
 * @property int $tipo_lapso_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AnoAcademico $anoAcademico
 * @property-read string $estado
 * @property-read Carbon $fin_real
 * @property-read Carbon $inicio_real
 * @property-read \App\Models\TipoLapso $tipoLapso
 * @method static Builder<static>|LapsoAcademico activos()
 * @method static Builder<static>|LapsoAcademico newModelQuery()
 * @method static Builder<static>|LapsoAcademico newQuery()
 * @method static Builder<static>|LapsoAcademico query()
 * @method static Builder<static>|LapsoAcademico whereAnoAcademicoId($value)
 * @method static Builder<static>|LapsoAcademico whereCreatedAt($value)
 * @method static Builder<static>|LapsoAcademico whereFechaFin($value)
 * @method static Builder<static>|LapsoAcademico whereFechaInicio($value)
 * @method static Builder<static>|LapsoAcademico whereId($value)
 * @method static Builder<static>|LapsoAcademico whereNombreLapso($value)
 * @method static Builder<static>|LapsoAcademico whereStatus($value)
 * @method static Builder<static>|LapsoAcademico whereTipoLapsoId($value)
 * @method static Builder<static>|LapsoAcademico whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class LapsoAcademico extends Model
{
    use Auditable;

    protected $fillable = [
        'nombre_lapso',
        'fecha_inicio',
        'fecha_fin',
        'ano_academico_id',
        'tipo_lapso_id',
        'status'
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
    ];

    protected $appends = ['estado'];

    protected $attributes = [
        'status' => 'planificacion',
    ];

    /**
     * Resoluciones de herencia de fechas
     */
    public function getInicioRealAttribute(): Carbon
    {
        return $this->fecha_inicio ?? $this->anoAcademico->fecha_inicio;
    }

    public function getFinRealAttribute(): Carbon
    {
        return $this->fecha_fin ?? $this->anoAcademico->fecha_fin;
    }


    /**
     * Accesor para calcular o leer el estado del lapso
     */
    public function getEstadoAttribute(): string
    {
        // 1. Clausura por el Padre
        if ($this->anoAcademico && $this->anoAcademico->estado_real === 'cerrado') {
            return 'cerrado';
        }

        // 2. Respetar la voluntad manual
        if ($this->status !== 'cronograma' && !empty($this->status)) {
            return $this->status;
        }

        // 3. Lógica por defecto (Planificación)
        return 'planificacion';
    }

    /**
     * SCOPE: Filtra lapsos vigentes hoy para los procesos ordinarios del sistema
     */
    public function scopeActivos(Builder $query): void
    {
        $hoy = Carbon::today()->toDateString();

        // 1. Encapsular la lógica del Año Académico Padre para que el OR no rompa la consulta
        $query->whereHas('anoAcademico', function ($q) use ($hoy) {
            $q->where(function ($subAño) use ($hoy) {
                $subAño->where('status', 'activo')
                    ->orWhere(function ($cronograma) use ($hoy) {
                        $cronograma->where('status', 'cronograma')
                            ->where('fecha_inicio', '<=', $hoy)
                            ->where('fecha_fin', '>=', $hoy);
                    });
            });
        });

        // 2. Filtrar el Lapso de forma estricta por su propio estado
        $query->where(function ($q) use ($hoy) {
            // Criterio A: El lapso fue activado manualmente mediante el Switch (status físico = 'activo')
            $q->where('status', 'activo')

                // Criterio B: O el lapso se maneja por cronograma y hoy cae dentro de su rango de fechas
                ->orWhere(function ($subQ) use ($hoy) {
                    $subQ->where('status', 'cronograma')
                        ->where(function ($fechas) use ($hoy) {
                            $fechas->whereNull('fecha_inicio') // Si hereda del padre y no tiene fechas propias
                                ->orWhere(function ($rango) use ($hoy) {
                                    $rango->where('fecha_inicio', '<=', $hoy)
                                        ->where('fecha_fin', '>=', $hoy);
                                });
                        });
                });
        });
    }

    /**
     * SCOPE: orden cronológico estable (el más antiguo primero).
     * Se usa para que frontend y backend resuelvan el "lapso actual" con el
     * mismo criterio: el primer período vigente es el que se selecciona por
     * defecto cuando el cliente todavía no ha elegido uno.
     */
    public function scopeOrdenCronologico(Builder $query): void
    {
        $query->orderBy('fecha_inicio')->orderBy('id');
    }

    /**
     * Lapso que el sistema asume como "actual" cuando la petición no trae
     * contexto (por ejemplo, primer inicio de sesión o localStorage vacío).
     *
     * 1. El lapso vigente hoy (scope activos), en orden cronológico.
     * 2. Si no hay ninguno vigente, el lapso más reciente del año académico
     *    más reciente (permite operar el panel para activar/crear lapsos).
     */
    public static function actual(): ?self
    {
        return static::query()->activos()->ordenCronologico()->first()
            ?? static::query()
                ->orderByDesc('ano_academico_id')
                ->orderBy('fecha_inicio')
                ->orderBy('id')
                ->first();
    }

    // Método para La relacion con el Año Académico
    public function anoAcademico()
    {
        return $this->belongsTo(AnoAcademico::class, 'ano_academico_id');
    }

    // Relación con los tipos de lapsos
    public function tipoLapso()
    {
        return $this->belongsTo(TipoLapso::class, 'tipo_lapso_id');
    }

    public function registrosPer()
    {
        return $this->hasMany(RegistroPer::class, 'lapso_academico_id');
    }
}
