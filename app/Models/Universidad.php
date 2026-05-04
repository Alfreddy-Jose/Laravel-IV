<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Universidad extends Model
{
    protected $table = 'universidads';

    protected $fillable = [
        'nombre_univ',
        'abreviado_univ',
        'rif_univ',
        'direccion',
        'logo',
    ];
}
