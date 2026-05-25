<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CondicionContrato extends Model
{
    public $fillable = [
        "fecha_inicio",
        "fecha_fin",
        "dedicacion",
        "tipo", 
        "docente_id"
    ];
    
    // Relación con la tabla docentes
    public function docente()
    {
        return $this->belongsTo(Docente::class);
    }
}
