<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Trayecto extends Model
{

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
