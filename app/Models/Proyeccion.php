<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property int $ano_academico_id
 * @property int $pnf_id
 * @property string $estado
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AnoAcademico $anoAcademico
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProyeccionSeccion> $detalles
 * @property-read int|null $detalles_count
 * @property-read \App\Models\Pnf $pnf
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Proyeccion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Proyeccion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Proyeccion query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Proyeccion whereAnoAcademicoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Proyeccion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Proyeccion whereEstado($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Proyeccion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Proyeccion wherePnfId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Proyeccion whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Proyeccion extends Model
{
    use HasFactory;

    protected $table = 'proyecciones';

    protected $fillable = [
        'ano_academico_id',
        'pnf_id',
        'estado',
    ];

// Relación: Una proyección pertenece a un Lapso Académico
    public function anoAcademico()
    {
        // Ajusta 'AnoAcademico::class' según el nombre exacto de tu modelo de años
        return $this->belongsTo(AnoAcademico::class, 'ano_academico_id');
    }

    /**
     * Relación: Una proyección pertenece a un PNF específico.
     */
    public function pnf()
    {
        return $this->belongsTo(Pnf::class, 'pnf_id');
    }

    /**
     * Relación: Una proyección global tiene muchas secciones detalladas (las filas del Excel).
     */
    public function detalles()
    {
        return $this->hasMany(ProyeccionSeccion::class, 'proyeccion_id');
    }
}
