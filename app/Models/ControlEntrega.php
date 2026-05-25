<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ControlEntrega extends Model
{
    protected $fillable = [
        'entregado',
        'fecha_entrega',
        'observaciones',
        'docente_id',
        'lapso_academico_id',
        'tipo_documento_id',
        'seccion_id'
    ];

    protected $casts = ['entregado' => 'boolean'];

    public function tipoDocumento(): BelongsTo
    {
        return $this->belongsTo(TipoDocumento::class);
    }
    public function docente(): BelongsTo
    {
        return $this->belongsTo(Docente::class, 'docente_id');
    }
    public function seccion(): BelongsTo
    {
        return $this->belongsTo(Seccion::class);
    }
}
