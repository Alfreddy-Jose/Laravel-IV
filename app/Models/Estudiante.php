<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

/**
 * @property int $id
 * @property int $persona_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Censo> $censos
 * @property-read int|null $censos_count
 * @property-read \App\Models\Persona $persona
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Seccion> $secciones
 * @property-read int|null $secciones_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Estudiante newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Estudiante newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Estudiante query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Estudiante whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Estudiante whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Estudiante wherePersonaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Estudiante whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Estudiante extends Model
{
    use Auditable;
    protected $table = 'estudiantes';

    protected $fillable = [
        'persona_id'
    ];

    /**
     * Relación Uno a Uno (Inversa) con Persona.
     * Obtiene los datos personales del estudiante.
     */
    public function persona()
    {
        return $this->belongsTo(Persona::class);
    }

    /**
     * Relación Muchos a Muchos con Secciones (Matrícula Histórica).
     * Utiliza tu tabla pivote 'estudiantes_seccion'.
     */
    public function secciones()
    {
        return $this->belongsToMany(
            Seccion::class,
            'estudiante_seccion',
            'estudiante_id',
            'seccion_id'
        )->withPivot('estatus')->withTimestamps();
    }

    /**
     * Relación Uno a Muchos con Vocero.
     * Un estudiante puede registrar múltiples vocerías a lo largo del tiempo.
     */
    /*    public function vocerias()
        {
            return $this->hasMany(Vocero::class, 'estudiante_id');
        }*/

    public function censos()
    {
        return $this->hasMany(Censo::class, 'estudiante_id');
    }

    public function registrosPer()
    {
        return $this->hasMany(RegistroPer::class, 'estudiante_id');
    }
}
