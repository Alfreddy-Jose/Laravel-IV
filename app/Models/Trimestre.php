<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $nombre
 * @property int $numero_relativo
 * @property int $trayecto_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $nombre_relativo
 * @property-read \App\Models\Trayecto $trayecto
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UnidadCurricular> $unidadCurriculares
 * @property-read int|null $unidad_curriculares_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trimestre newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trimestre newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trimestre query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trimestre whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trimestre whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trimestre whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trimestre whereNumeroRelativo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trimestre whereTrayectoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trimestre whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Trimestre extends Model
{
    protected $fillable = ['nombre', 'trayecto_id', 'numero_relativo'];

    protected $appends = ['nombre_relativo'];


    /**
     * Accessor para mostrar siempre I, II, III en la interfaz
     */
    public function getNombreRelativoAttribute()
    {
        $romanos = [1 => 'I', 2 => 'II', 3 => 'III'];
        return $romanos[$this->numero_relativo] ?? $this->nombre;
    }

    public function unidadCurriculares()
    {
        return $this->belongsToMany(UnidadCurricular::class);
    }

    public function trayecto()
    {
        return $this->belongsTo(Trayecto::class);
    }
}
