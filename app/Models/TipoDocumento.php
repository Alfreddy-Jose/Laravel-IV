<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoDocumento extends Model
{
    protected $fillable = ['nombre', 'alcance'];

    // Relación inversa: Un tipo de documento tiene muchas entregas asociadas
    public function entregas()
    {
        return $this->hasMany(ControlEntrega::class);
    }
}
