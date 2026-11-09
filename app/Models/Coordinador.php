<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

/**
 * @property int $id
 * @property int|null $docente_id
 * @property int $pnf_id
 * @property \Illuminate\Support\Carbon $fecha_inicio
 * @property \Illuminate\Support\Carbon|null $fecha_fin
 * @property bool $activo
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Docente|null $docente
 * @property-read \App\Models\Pnf $pnf
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coordinador activo()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coordinador historial()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coordinador newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coordinador newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coordinador query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coordinador whereActivo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coordinador whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coordinador whereDocenteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coordinador whereFechaFin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coordinador whereFechaInicio($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coordinador whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coordinador wherePnfId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coordinador whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Coordinador extends Model
{
    use Auditable;

    protected $table = 'coordinadors';

    protected $fillable = [
        'docente_id',
        'pnf_id',
        'fecha_inicio',
        'fecha_fin',
        'activo',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'activo' => 'boolean',
    ];

    // Relación con la tabla Docentes
    public function docente()
    {
        return $this->belongsTo(Docente::class);
    }

    // Relación con la tabla PNF
    public function pnf()
    {
        return $this->belongsTo(Pnf::class);
    }

    /**
     * Scope para activos
     */
    public function scopeActivo($query)
    {
        return $query->where('activo', true)
            ->whereNull('fecha_fin');
    }

    /**
     * Scope para historial
     */
    public function scopeHistorial($query)
    {
        return $query->where('activo', false)
            ->orWhereNotNull('fecha_fin');
    }
}
