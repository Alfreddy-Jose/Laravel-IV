<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Auditable;

/**
 * @property int $id
 * @property int $estudiante_id
 * @property int $lapso_id
 * @property string $nombre
 * @property string $tipo_censo
 * @property array<array-key, mixed>|null $detalles
 * @property string|null $observaciones
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Estudiante $estudiante
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Censo deLaSeccion($seccionId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Censo delLapso($lapsoId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Censo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Censo newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Censo porCategoria($categoria)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Censo porCondicion($condicion)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Censo query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Censo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Censo whereDetalles($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Censo whereEstudianteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Censo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Censo whereLapsoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Censo whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Censo whereObservaciones($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Censo whereTipoCenso($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Censo whereUpdatedAt($value)
 * @property-read \App\Models\LapsoAcademico $lapso
 * @mixin \Eloquent
 */
class Censo extends Model
{
    use HasFactory;
    use Auditable;

    protected $table = 'censos';

    protected $fillable = [
        'estudiante_id',
        'lapso_id',
        'nombre',
        'tipo_censo',
        'detalles', // Recibe directamente el JSON serializado de React
        'observaciones'
    ];

    /**
     * Casting de atributos: Transforma el formato JSON de PostgreSQL
     * a un Array Asociativo de PHP de manera automática.
     */
    protected $casts = [
        'detalles' => 'array'
    ];

    /**
     * Relación: Censo pertenece a un Estudiante (Inversa de Tiene)
     */
    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class, 'estudiante_id');
    }

    /**
     * Relación: El censo se efectúa en un Lapso Académico específico
     */
    public function lapso()
    {
        return $this->belongsTo(LapsoAcademico::class, 'lapso_id');
    }

    // =========================================================================
    // SCOPES DE CONSULTA AVANZADA (Indexación y filtrado sobre la columna JSON)
    // =========================================================================

    /**
     * Filtrar censos pertenecientes a un lapso específico
     */
    public function scopeDelLapso($query, $lapsoId)
    {
        return $query->where('lapso_id', $lapsoId);
    }

    /**
     * Mapea la optimización del JSON buscando por clave interna (Sintaxis -> de PostgreSQL)
     * Ej: Censo::porCategoria('discapacidad')->get();
     */
    public function scopePorCategoria($query, $categoria)
    {
        return $query->where('detalles->categoria', $categoria);
    }

    /**
     * Filtrar por condiciones médicas específicas dentro del JSON (Autismo, Asperger, etc.)
     */
    public function scopePorCondicion($query, $condicion)
    {
        return $query->where('detalles->condicion', $condicion);
    }

    /**
     * Reporte solicitado por el coordinador: Filtrar todos los censos por la Sección de procedencia
     */
    public function scopeDeLaSeccion($query, $seccionId)
    {
        return $query->whereHas('estudiante.secciones', function ($q) use ($seccionId) {
            $q->where('seccions.id', $seccionId); // Apunta a tu tabla 'seccions'
        });
    }
}
