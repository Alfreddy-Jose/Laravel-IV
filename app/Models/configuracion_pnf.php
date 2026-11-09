<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property int $pnf_trayecto_id
 * @property int $max_estudiantes_seccion
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|configuracion_pnf newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|configuracion_pnf newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|configuracion_pnf query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|configuracion_pnf whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|configuracion_pnf whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|configuracion_pnf whereMaxEstudiantesSeccion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|configuracion_pnf wherePnfTrayectoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|configuracion_pnf whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class configuracion_pnf extends Model
{
    use HasFactory;

    // Indicamos explícitamente el nombre de la tabla por el plural en español
    protected $table = 'configuracion_pnfs';

    protected $fillable = [
        'pnf_trayecto_id',
        'max_estudiantes_seccion',
    ];
}
