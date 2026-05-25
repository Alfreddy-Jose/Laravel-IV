<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coordinador extends Model
{
    protected $table = 'coordinadors';

    protected $fillable = [
        'docente_id',
        'pnf_id',
        'fecha_inicio',
        'fecha_fin',
        'activo',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'activo' => 'boolean',
    ];

    // Relación con la tabla Docentes
    public function docente()
    {
        return $this->belongsTo(Docente::class);
    }

    // Relación con la tabla PNF
    public function pnf()
    {
        return $this->belongsTo(Pnf::class);
    }

    /**
     * Scope para activos
     */
    public function scopeActivo($query)
    {
        return $query->where('activo', true)
            ->whereNull('fecha_fin');
    }

    /**
     * Scope para historial
     */
    public function scopeHistorial($query)
    {
        return $query->where('activo', false)
            ->orWhereNotNull('fecha_fin');
    }
}
