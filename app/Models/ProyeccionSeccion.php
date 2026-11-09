<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


/**
 * @property int $grupos_aprobados
 * @property int $grupos_reprobados
 * @property int $id
 * @property int $proyeccion_id
 * @property int $sede_id
 * @property int $trayecto_id
 * @property string $seccion_origen
 * @property string $seccion_destino
 * @property bool $crear_seccion
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $alumnos_origen_aprobados
 * @property-read mixed $alumnos_origen_reprobados
 * @property-read \App\Models\Proyeccion $proyeccion
 * @property-read \App\Models\Sede $sede
 * @property-read \App\Models\Trayecto $trayecto
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProyeccionSeccion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProyeccionSeccion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProyeccionSeccion query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProyeccionSeccion whereCrearSeccion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProyeccionSeccion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProyeccionSeccion whereGruposAprobados($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProyeccionSeccion whereGruposReprobados($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProyeccionSeccion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProyeccionSeccion whereProyeccionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProyeccionSeccion whereSeccionDestino($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProyeccionSeccion whereSeccionOrigen($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProyeccionSeccion whereSedeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProyeccionSeccion whereTrayectoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProyeccionSeccion whereUpdatedAt($value)
 * @mixin \Eloquent
 */

class ProyeccionSeccion extends Model
{
    use HasFactory;

    protected $table = 'proyeccion_secciones';

    protected $fillable = [
        'proyeccion_id',
        'sede_id',
        'trayecto_id',
        'seccion_origen',
        'seccion_destino',
        'grupos_aprobados',
        'grupos_reprobados',
        'crear_seccion',
    ];

    // Forzamos el casteo del switch para que React lo reciba como un Booleano puro (true/false)
    protected $casts = [
        'crear_seccion' => 'boolean',
    ];

    /**
     * Appends inserta automáticamente los campos calculados
     * en el JSON cuando React consulte la API.
     */
    protected $appends = [
        'alumnos_origen_aprobados',
        'alumnos_origen_reprobados',
    ];

    // ==========================================
    //  ACCESSORS (Campos calculados al vuelo)
    // ==========================================

    /**
     * Calcula los alumnos origen aprobados basándose en los grupos ingresados (Multiplicado por 5)
     */
    public function getAlumnosOrigenAprobadosAttribute()
    {
        return $this->grupos_aprobados * 5;
    }

    /**
     * Calcula los alumnos origen reprobados basándose en los grupos (Multiplicado por 5)
     */
    public function getAlumnosOrigenReprobadosAttribute()
    {
        return $this->grupos_reprobados * 5;
    }

    // ==========================================
    // 🔗 RELACIONES DE ELOQUENT
    // ==========================================

    /**
     * Relación inversa: Este detalle pertenece a una cabecera de Proyección.
     */
    public function proyeccion()
    {
        return $this->belongsTo(Proyeccion::class, 'proyeccion_id');
    }

    /**
     * Relación directa: Conocer la sede física a la que pertenece esta fila.
     */
    public function sede()
    {
        return $this->belongsTo(Sede::class, 'sede_id');
    }

    /**
     * Relación directa: Conocer el trayecto académico de destino de esta fila.
     */
    public function trayecto()
    {
        return $this->belongsTo(Trayecto::class, 'trayecto_id');
    }
}
