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
    ];
}
