<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    protected $fillable = [
        'cedula_persona',
        'nombre',
        'apellido',
        'direccion',
        'telefono',
        'email',
        'tipo_persona',
        'grado_inst',
        'municipio_id',
    ];
    // Relación con el modelo Tipo_persona
/*     public function tipo_persona()
    {
        return $this->hasMany(Tipo_persona::class, 'persona_id');
    } */

    // Relacion con el modelo Docente
/*    public function docente()
    {
        return $this->hasOne(Docente::class);
    }*/

    // Relacion con el modelo Vocero
/*    public function vocero()
    {
        return $this->hasOne(Vocero::class);
    }*/

    // Relacion con la tabla de municipios
    public function municipio()
    {
        return $this->belongsTo(Municipio::class, 'municipio_id', 'id_municipio');
    }

    // Relacion con la tabla de usuarios
/*    public function user()
    {
        return $this->hasOne(User::class);
    }*/
}
