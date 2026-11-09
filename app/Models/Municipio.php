<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id_municipio
 * @property int $id_estado
 * @property string $municipio
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Estado $estado
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Sede> $sedes
 * @property-read int|null $sedes_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipio newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipio newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipio query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipio whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipio whereIdEstado($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipio whereIdMunicipio($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipio whereMunicipio($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipio whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Municipio extends Model
{
    use HasFactory;

    protected $table = 'municipios';
    protected $primaryKey = 'id_municipio';
    public $incrementing = false; // Si id_municipio no es autoincremental
    protected $keyType = 'integer';

    protected $fillable = [
        'id_municipio',
        'id_estado',
        'municipio'
    ]; 

    // Relacion con estado
    public function estado()
    {
        return $this->belongsTo(Estado::class, 'id_estado', 'id_estado');
    }

    // Relacion con sedes
    public function sedes()
    {
        return $this->hasMany(Sede::class, 'municipio_id', 'id_municipio');
    }
}
