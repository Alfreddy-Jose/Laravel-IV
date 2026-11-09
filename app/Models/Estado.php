<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id_estado
 * @property string $estado
 * @property string $iso_3166
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Municipio> $municipios
 * @property-read int|null $municipios_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Estado newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Estado newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Estado query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Estado whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Estado whereEstado($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Estado whereIdEstado($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Estado whereIso3166($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Estado whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Estado extends Model
{
    protected $table = 'estados';
    protected $primaryKey = 'id_estado';
    public $incrementing = false; // Si id_estado no es autoincremental
    protected $keyType = 'integer';

    protected $fillable = [
        'id_estado',
        'estado',
        'iso_3166'
    ];

    // Relacion con Municipio
    public function municipios()
    {
        return $this->hasMany(Municipio::class);
    }
}
