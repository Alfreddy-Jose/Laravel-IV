<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
