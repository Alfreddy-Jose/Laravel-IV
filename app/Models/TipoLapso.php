<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoLapso extends Model
{
    use HasFactory;

    public function lapsoAcademicos()
    {
        return $this->hasMany(LapsoAcademico::class);
    }
}
