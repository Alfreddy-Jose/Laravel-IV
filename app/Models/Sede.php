<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sede extends Model
{
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
