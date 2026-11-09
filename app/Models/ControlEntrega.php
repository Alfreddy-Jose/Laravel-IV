<?php

namespace App\Models;

use App\Models\Scopes\LapsoAcademicoScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Auditable;

/**
 * @property int $id
 * @property bool $entregado
 * @property string|null $fecha_entrega
 * @property string|null $archivo_path
 * @property string|null $observaciones
 * @property int $docente_id
 * @property int|null $unidad_curricular_id
 * @property int $lapso_academico_id
 * @property int $tipo_documento_id
 * @property int|null $seccion_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Docente $docente
 * @property-read \App\Models\Seccion|null $seccion
 * @property-read \App\Models\TipoDocumento $tipoDocumento
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ControlEntrega newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ControlEntrega newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ControlEntrega query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ControlEntrega whereArchivoPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ControlEntrega whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ControlEntrega whereDocenteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ControlEntrega whereEntregado($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ControlEntrega whereFechaEntrega($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ControlEntrega whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ControlEntrega whereLapsoAcademicoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ControlEntrega whereObservaciones($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ControlEntrega whereSeccionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ControlEntrega whereTipoDocumentoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ControlEntrega whereUnidadCurricularId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ControlEntrega whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ControlEntrega extends Model
{
    use Auditable;

    protected $fillable = [
        'entregado',
        'fecha_entrega',
        'observaciones',
        'docente_id',
        'unidad_curricular_id',
        'lapso_academico_id',
        'tipo_documento_id',
        'seccion_id',
        'archivo_path'
    ];

    protected static function booted(): void
    {
        // Activamos el filtro global inteligente
        static::addGlobalScope(new LapsoAcademicoScope());
    }

    protected $casts = ['entregado' => 'boolean'];

    public function tipoDocumento(): BelongsTo
    {
        return $this->belongsTo(TipoDocumento::class);
    }
    public function docente(): BelongsTo
    {
        return $this->belongsTo(Docente::class, 'docente_id');
    }
    public function seccion(): BelongsTo
    {
        return $this->belongsTo(Seccion::class);
    }
}
