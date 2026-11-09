<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

/**
 * @property int $id
 * @property int $nro_sede
 * @property string $nombre_sede
 * @property string $nombre_abreviado
 * @property string $direccion
 * @property int $universidad_id
 * @property int $municipio_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Municipio $municipio
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Pnf> $pnfs
 * @property-read int|null $pnfs_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Seccion> $secciones
 * @property-read int|null $secciones_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sede newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sede newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sede query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sede whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sede whereDireccion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sede whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sede whereMunicipioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sede whereNombreAbreviado($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sede whereNombreSede($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sede whereNroSede($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sede whereUniversidadId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sede whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Sede extends Model
{
    use Auditable;

    protected $fillable = [
        'nro_sede',
        'nombre_sede',
        'nombre_abreviado',
        'direccion',
        'municipio_id',
        'universidad_id',
        'pnf_id'
    ];

    public function secciones()
    {
        return $this->hasMany(Seccion::class);
    }

    // Relacion con la tabla de municipios
    public function municipio()
    {
        return $this->belongsTo(Municipio::class, 'municipio_id', 'id_municipio');
    }

    // Relacion muchos a muchos con el modelo Pnf
    public function pnfs()
    {
        return $this->belongsToMany(Pnf::class);
    }
}
