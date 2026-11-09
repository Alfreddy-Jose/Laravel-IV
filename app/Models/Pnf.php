<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

/**
 * @property int $id
 * @property string $codigo
 * @property string $nombre
 * @property string $abreviado
 * @property string $abreviado_coord
 * @property string|null $logo
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Docente> $docentes
 * @property-read int|null $docentes_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Sede> $sedes
 * @property-read int|null $sedes_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Trayecto> $trayectos
 * @property-read int|null $trayectos_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pnf newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pnf newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pnf query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pnf whereAbreviado($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pnf whereAbreviadoCoord($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pnf whereCodigo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pnf whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pnf whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pnf whereLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pnf whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pnf whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Pnf extends Model
{
    use Auditable;
    protected $fillable = [
        'id',
        'codigo',
        'nombre',
        'abreviado',
        'abreviado_coord',
        'logo',
    ];

    // Relación con el modelo Seccion
    /*    public function secciones()
        {
            return $this->hasMany(Seccion::class, 'pnf_id');
        }  */

    public function docentes()
    {
        return $this->hasMany(Docente::class);
    }

    // Relación muchos a muchos con el modelo Sede
    public function sedes()
    {
        return $this->belongsToMany(Sede::class);
    }

    // Relación mucho a muchos con el modelo trayectos
    public function trayectos()
    {
        return $this->belongsToMany(Trayecto::class, 'pnf_trayecto');
    }

    /**
     * Relación: Un PNF tiene muchos usuarios.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'pnf_id');
    }
}
