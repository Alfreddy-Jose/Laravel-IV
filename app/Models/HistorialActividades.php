<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistorialActividades extends Model
{
    use HasFactory;

    // Indicamos explícitamente el nombre de la tabla
    protected $table = 'historial_actividades';

    // Campos permitidos para llenado masivo
    protected $fillable = [
        'control_entrega_id',
        'user_id',
        'accion'
    ];

    /**
     * Relación: Una actividad pertenece a un usuario (quien hizo la acción).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relación: Una actividad está asociada a un registro de control de entrega.
     * De aquí extraeremos el docente y el documento.
     */
    public function controlEntrega(): BelongsTo
    {
        return $this->belongsTo(ControlEntrega::class, 'control_entrega_id');
    }
}
