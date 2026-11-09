<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $control_entrega_id
 * @property int $user_id
 * @property string $accion
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ControlEntrega $controlEntrega
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialActividades newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialActividades newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialActividades query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialActividades whereAccion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialActividades whereControlEntregaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialActividades whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialActividades whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialActividades whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialActividades whereUserId($value)
 * @mixin \Eloquent
 */
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
