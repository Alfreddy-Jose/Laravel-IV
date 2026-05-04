<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
