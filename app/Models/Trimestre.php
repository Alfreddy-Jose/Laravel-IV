<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
