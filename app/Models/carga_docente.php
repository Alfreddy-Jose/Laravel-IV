<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property int $docente_unidad_curricular_id
 * @property int $seccion_id
 * @property int $horas
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Seccion $seccion
 * @method static \Illuminate\Database\Eloquent\Builder<static>|carga_docente newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|carga_docente newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|carga_docente query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|carga_docente whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|carga_docente whereDocenteUnidadCurricularId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|carga_docente whereHoras($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|carga_docente whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|carga_docente whereSeccionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|carga_docente whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class carga_docente extends Model
{
    use HasFactory;

    protected $table = 'carga_docentes';

    protected $fillable = [
        'docente_unidad_id',
        'seccion_id',
        'horas',
    ];

    // Relación: La carga pertenece a una combinación específica de Docente-Unidad
    public function docenteUnidad()
    {
        return $this->belongsTo(DocenteUnidad::class, 'docente_unidad_id');
    }

    // Relación: La carga pertenece a una Sección (Base o Contingencia)
    public function seccion()
    {
        return $this->belongsTo(Seccion::class, 'seccion_id');
    }
}
