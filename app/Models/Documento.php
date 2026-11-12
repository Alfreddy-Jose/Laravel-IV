<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class Documento extends Model
{
    use Auditable;

    protected $fillable = [
        'pnf_id',
        'user_id',
        'nombre',
        'archivo',
        'tipo',
        'tamano',
    ];

    public function pnf()
    {
        return $this->belongsTo(Pnf::class, 'pnf_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
