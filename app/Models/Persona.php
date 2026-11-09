<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

/**
 * @property int $id
 * @property int $cedula_persona
 * @property string $nombre
 * @property string $apellido
 * @property string|null $direccion
 * @property string $telefono
 * @property string $email
 * @property string $tipo_persona
 * @property string $grado_inst
 * @property int $municipio_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Docente|null $docente
 * @property-read \App\Models\Estudiante|null $estudiante
 * @property-read \App\Models\Municipio $municipio
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persona newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persona newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persona query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persona whereApellido($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persona whereCedulaPersona($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persona whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persona whereDireccion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persona whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persona whereGradoInst($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persona whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persona whereMunicipioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persona whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persona whereTelefono($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persona whereTipoPersona($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persona whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Persona extends Model
{
    use Auditable;

    protected $fillable = [
        'cedula_persona',
        'nombre',
        'apellido',
        'direccion',
        'telefono',
        'email',
        'tipo_persona',
        'grado_inst',
        'municipio_id',
    ];


    // Relacion con el modelo Docente
    public function docente()
    {
        return $this->hasOne(Docente::class);
    }

    // Relacion con la tabla de municipios
    public function municipio()
    {
        return $this->belongsTo(Municipio::class, 'municipio_id', 'id_municipio');
    }

    /**
     * Relación Uno a Uno con Estudiante.
     * Una persona puede tener un perfil de estudiante.
     */
    public function estudiante()
    {
        return $this->hasOne(Estudiante::class);
    }
}
