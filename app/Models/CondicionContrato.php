<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $fecha_inicio
 * @property string $fecha_fin
 * @property string $dedicacion
 * @property string $tipo
 * @property int $docente_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Docente $docente
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CondicionContrato newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CondicionContrato newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CondicionContrato query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CondicionContrato whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CondicionContrato whereDedicacion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CondicionContrato whereDocenteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CondicionContrato whereFechaFin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CondicionContrato whereFechaInicio($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CondicionContrato whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CondicionContrato whereTipo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CondicionContrato whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CondicionContrato extends Model
{
    public $fillable = [
        "fecha_inicio",
        "fecha_fin",
        "dedicacion",
        "tipo",
        "docente_id"
    ];

    // Relación con la tabla docentes
    public function docente()
    {
        return $this->belongsTo(Docente::class);
    }
}
