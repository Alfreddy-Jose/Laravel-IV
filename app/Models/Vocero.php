<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

/**
 * @property int $id
 * @property int $estudiante_id
 * @property int $seccion_id
 * @property string $tipo_vocero
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Estudiante $estudiante
 * @property-read \App\Models\Seccion $seccion
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vocero newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vocero newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vocero query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vocero whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vocero whereEstudianteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vocero whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vocero whereSeccionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vocero whereTipoVocero($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vocero whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Vocero extends Model
{
    use Auditable;

    protected $table = 'voceros';

    protected $fillable = [
        'estudiante_id',
        'seccion_id', // Se guarda físicamente para asegurar unicidad y rapidez
        'tipo_vocero', // 'Principal' o 'Suplente'
    ];

    /**
     * Relación Uno a Muchos (Inversa) con Estudiante.
     * El estudiante al que pertenece esta vocería.
     */
    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class, 'estudiante_id');
    }

    /**
     * Relación Uno a Muchos (Inversa) con Seccion.
     * Aunque no esté graficada la línea, Eloquent la entiende gracias al campo físico.
     */
    public function seccion()
    {
        return $this->belongsTo(Seccion::class);
    }

    /**
     * Acceso directo al lapso académico a través de la sección
     */
    public function lapso(): HasOneThrough
    {
        return $this->hasOneThrough(
            LapsoAcademico::class,
            Seccion::class,
            'id',          // Foreign key en secciones (secciones.id)
            'id',          // Foreign key en lapsos (lapsos.id)
            'seccion_id',  // Local key en voceros
            'lapso_id'     // Local key en secciones
        );
    }
}
