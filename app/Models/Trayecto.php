<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

/**
 * @property int $id
 * @property string $nombre
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Pnf> $pnfs
 * @property-read int|null $pnfs_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Trimestre> $trimestres
 * @property-read int|null $trimestres_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trayecto newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trayecto newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trayecto query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trayecto whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trayecto whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trayecto whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trayecto whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Trayecto extends Model
{
    use Auditable;

    public $fillable = [
        'nombre'
    ];

    /*    public function secciones()
        {
            return $this->hasMany(Seccion::class);
        }*/

    public function trimestres()
    {
        return $this->hasMany(Trimestre::class);
    }

    // Relación mucho a muchos con el modelo pnf
    public function pnfs()
    {
        return $this->belongsToMany(Pnf::class, 'pnf_trayecto');
    }
}
