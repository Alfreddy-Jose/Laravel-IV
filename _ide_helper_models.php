<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property int $ano
 * @property \Illuminate\Support\Carbon $fecha_inicio
 * @property \Illuminate\Support\Carbon $fecha_fin
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $estado_real
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LapsoAcademico> $lapsos
 * @property-read int|null $lapsos_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AnoAcademico newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AnoAcademico newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AnoAcademico query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AnoAcademico whereAno($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AnoAcademico whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AnoAcademico whereFechaFin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AnoAcademico whereFechaInicio($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AnoAcademico whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AnoAcademico whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AnoAcademico whereUpdatedAt($value)
 */
	class AnoAcademico extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $estudiante_id
 * @property int $lapso_id
 * @property string $nombre
 * @property string $tipo_censo
 * @property array<array-key, mixed>|null $detalles
 * @property string|null $observaciones
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Estudiante $estudiante
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Censo deLaSeccion($seccionId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Censo delLapso($lapsoId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Censo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Censo newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Censo porCategoria($categoria)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Censo porCondicion($condicion)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Censo query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Censo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Censo whereDetalles($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Censo whereEstudianteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Censo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Censo whereLapsoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Censo whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Censo whereObservaciones($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Censo whereTipoCenso($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Censo whereUpdatedAt($value)
 */
	class Censo extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $fecha_inicio
 * @property string $fecha_fin
 * @property string $dedicacion
 * @property string $tipo
 * @property int $docente_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Docente $docente
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CondicionContrato newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CondicionContrato newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CondicionContrato query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CondicionContrato whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CondicionContrato whereDedicacion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CondicionContrato whereDocenteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CondicionContrato whereFechaFin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CondicionContrato whereFechaInicio($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CondicionContrato whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CondicionContrato whereTipo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CondicionContrato whereUpdatedAt($value)
 */
	class CondicionContrato extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property bool $entregado
 * @property string|null $fecha_entrega
 * @property string|null $archivo_path
 * @property string|null $observaciones
 * @property int $docente_id
 * @property int|null $unidad_curricular_id
 * @property int $lapso_academico_id
 * @property int $tipo_documento_id
 * @property int|null $seccion_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Docente $docente
 * @property-read \App\Models\Seccion|null $seccion
 * @property-read \App\Models\TipoDocumento $tipoDocumento
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ControlEntrega newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ControlEntrega newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ControlEntrega query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ControlEntrega whereArchivoPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ControlEntrega whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ControlEntrega whereDocenteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ControlEntrega whereEntregado($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ControlEntrega whereFechaEntrega($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ControlEntrega whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ControlEntrega whereLapsoAcademicoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ControlEntrega whereObservaciones($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ControlEntrega whereSeccionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ControlEntrega whereTipoDocumentoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ControlEntrega whereUnidadCurricularId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ControlEntrega whereUpdatedAt($value)
 */
	class ControlEntrega extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $docente_id
 * @property int $pnf_id
 * @property \Illuminate\Support\Carbon $fecha_inicio
 * @property \Illuminate\Support\Carbon|null $fecha_fin
 * @property bool $activo
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Docente|null $docente
 * @property-read \App\Models\Pnf $pnf
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coordinador activo()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coordinador historial()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coordinador newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coordinador newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coordinador query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coordinador whereActivo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coordinador whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coordinador whereDocenteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coordinador whereFechaFin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coordinador whereFechaInicio($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coordinador whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coordinador wherePnfId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coordinador whereUpdatedAt($value)
 */
	class Coordinador extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $categoria
 * @property int $persona_id
 * @property int $pnf_id
 * @property int $horas_dedicacion
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\CondicionContrato|null $condicionContrato
 * @property-read \App\Models\Coordinador|null $coordinacionActiva
 * @property-read \App\Models\Coordinador|null $coordinador
 * @property-read \App\Models\Persona $persona
 * @property-read \App\Models\Pnf $pnf
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UnidadCurricular> $unidades_curriculares
 * @property-read int|null $unidades_curriculares_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Docente newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Docente newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Docente query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Docente whereCategoria($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Docente whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Docente whereHorasDedicacion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Docente whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Docente wherePersonaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Docente wherePnfId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Docente whereUpdatedAt($value)
 */
	class Docente extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id_estado
 * @property string $estado
 * @property string $iso_3166
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Municipio> $municipios
 * @property-read int|null $municipios_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Estado newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Estado newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Estado query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Estado whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Estado whereEstado($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Estado whereIdEstado($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Estado whereIso3166($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Estado whereUpdatedAt($value)
 */
	class Estado extends \Eloquent {}
}

namespace App\Models{
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
 */
	class Estudiante extends \Eloquent {}
}

namespace App\Models{
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
 */
	class HistorialActividades extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $nombre_lapso
 * @property \Illuminate\Support\Carbon|null $fecha_inicio
 * @property \Illuminate\Support\Carbon|null $fecha_fin
 * @property string $status
 * @property int $ano_academico_id
 * @property int $tipo_lapso_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AnoAcademico $anoAcademico
 * @property-read string $estado
 * @property-read \Carbon\Carbon $fin_real
 * @property-read \Carbon\Carbon $inicio_real
 * @property-read \App\Models\TipoLapso $tipoLapso
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LapsoAcademico activos()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LapsoAcademico newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LapsoAcademico newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LapsoAcademico query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LapsoAcademico whereAnoAcademicoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LapsoAcademico whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LapsoAcademico whereFechaFin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LapsoAcademico whereFechaInicio($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LapsoAcademico whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LapsoAcademico whereNombreLapso($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LapsoAcademico whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LapsoAcademico whereTipoLapsoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LapsoAcademico whereUpdatedAt($value)
 */
	class LapsoAcademico extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $numero
 * @property string $nombre
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Matricula newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Matricula newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Matricula query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Matricula whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Matricula whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Matricula whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Matricula whereNumero($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Matricula whereUpdatedAt($value)
 */
	class Matricula extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id_municipio
 * @property int $id_estado
 * @property string $municipio
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Estado $estado
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Sede> $sedes
 * @property-read int|null $sedes_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipio newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipio newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipio query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipio whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipio whereIdEstado($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipio whereIdMunicipio($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipio whereMunicipio($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipio whereUpdatedAt($value)
 */
	class Municipio extends \Eloquent {}
}

namespace App\Models{
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
 */
	class Persona extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $codigo
 * @property string $nombre
 * @property string $abreviado
 * @property string $abreviado_coord
 * @property string|null $logo
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Docente> $docentes
 * @property-read int|null $docentes_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Sede> $sedes
 * @property-read int|null $sedes_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Trayecto> $trayectos
 * @property-read int|null $trayectos_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pnf newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pnf newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pnf query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pnf whereAbreviado($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pnf whereAbreviadoCoord($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pnf whereCodigo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pnf whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pnf whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pnf whereLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pnf whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pnf whereUpdatedAt($value)
 */
	class Pnf extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $ano_academico_id
 * @property int $pnf_id
 * @property string $estado
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AnoAcademico $anoAcademico
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProyeccionSeccion> $detalles
 * @property-read int|null $detalles_count
 * @property-read \App\Models\Pnf $pnf
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Proyeccion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Proyeccion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Proyeccion query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Proyeccion whereAnoAcademicoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Proyeccion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Proyeccion whereEstado($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Proyeccion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Proyeccion wherePnfId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Proyeccion whereUpdatedAt($value)
 */
	class Proyeccion extends \Eloquent {}
}

namespace App\Models{
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
 */
	class ProyeccionSeccion extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $pnf_id
 * @property int $matricula_id
 * @property int $trayecto_id
 * @property int $sede_id
 * @property int $lapso_id
 * @property int $numero_seccion
 * @property string $nombre
 * @property string $tipo_seccion
 * @property int $cupo_maximo
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Estudiante> $estudiantes
 * @property-read int|null $estudiantes_count
 * @property-read \App\Models\LapsoAcademico $lapso
 * @property-read \App\Models\Matricula $matricula
 * @property-read \App\Models\Pnf $pnf
 * @property-read \App\Models\Sede $sede
 * @property-read \App\Models\Trayecto $trayecto
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Seccion dePnf($pnfId = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Seccion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Seccion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Seccion query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Seccion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Seccion whereCupoMaximo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Seccion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Seccion whereLapsoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Seccion whereMatriculaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Seccion whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Seccion whereNumeroSeccion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Seccion wherePnfId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Seccion whereSedeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Seccion whereTipoSeccion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Seccion whereTrayectoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Seccion whereUpdatedAt($value)
 */
	class Seccion extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $nro_sede
 * @property string $nombre_sede
 * @property string $nombre_abreviado
 * @property string $direccion
 * @property int $universidad_id
 * @property int $municipio_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Municipio $municipio
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Pnf> $pnfs
 * @property-read int|null $pnfs_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Seccion> $secciones
 * @property-read int|null $secciones_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sede newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sede newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sede query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sede whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sede whereDireccion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sede whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sede whereMunicipioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sede whereNombreAbreviado($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sede whereNombreSede($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sede whereNroSede($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sede whereUniversidadId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sede whereUpdatedAt($value)
 */
	class Sede extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $nombre
 * @property string $alcance
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ControlEntrega> $entregas
 * @property-read int|null $entregas_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipoDocumento newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipoDocumento newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipoDocumento query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipoDocumento whereAlcance($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipoDocumento whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipoDocumento whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipoDocumento whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipoDocumento whereUpdatedAt($value)
 */
	class TipoDocumento extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $nombre
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LapsoAcademico> $lapsoAcademicos
 * @property-read int|null $lapso_academicos_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipoLapso newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipoLapso newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipoLapso query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipoLapso whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipoLapso whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipoLapso whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipoLapso whereUpdatedAt($value)
 */
	class TipoLapso extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $nombre
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Pnf> $pnfs
 * @property-read int|null $pnfs_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Trimestre> $trimestres
 * @property-read int|null $trimestres_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trayecto newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trayecto newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trayecto query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trayecto whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trayecto whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trayecto whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trayecto whereUpdatedAt($value)
 */
	class Trayecto extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $nombre
 * @property int $numero_relativo
 * @property int $trayecto_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $nombre_relativo
 * @property-read \App\Models\Trayecto $trayecto
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UnidadCurricular> $unidadCurriculares
 * @property-read int|null $unidad_curriculares_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trimestre newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trimestre newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trimestre query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trimestre whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trimestre whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trimestre whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trimestre whereNumeroRelativo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trimestre whereTrayectoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trimestre whereUpdatedAt($value)
 */
	class Trimestre extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $nombre
 * @property string|null $descripcion
 * @property bool $es_proyecto
 * @property int $unidad_credito
 * @property int|null $hora_teorica
 * @property int|null $hora_practica
 * @property int $hora_total_est
 * @property string $periodo
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Docente> $docentes
 * @property-read int|null $docentes_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Trimestre> $trimestres
 * @property-read int|null $trimestres_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UnidadCurricular newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UnidadCurricular newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UnidadCurricular query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UnidadCurricular whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UnidadCurricular whereDescripcion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UnidadCurricular whereEsProyecto($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UnidadCurricular whereHoraPractica($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UnidadCurricular whereHoraTeorica($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UnidadCurricular whereHoraTotalEst($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UnidadCurricular whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UnidadCurricular whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UnidadCurricular wherePeriodo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UnidadCurricular whereUnidadCredito($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UnidadCurricular whereUpdatedAt($value)
 */
	class UnidadCurricular extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $nombre_univ
 * @property string $abreviado_univ
 * @property string $rif_univ
 * @property string $direccion
 * @property string|null $logo
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Universidad newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Universidad newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Universidad query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Universidad whereAbreviadoUniv($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Universidad whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Universidad whereDireccion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Universidad whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Universidad whereLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Universidad whereNombreUniv($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Universidad whereRifUniv($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Universidad whereUpdatedAt($value)
 */
	class Universidad extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string|null $avatar
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property int|null $persona_id
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $pnf
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \App\Models\Persona|null $persona
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User permission($permissions, bool $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User role($roles, ?string $guard = null, bool $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePersonaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutRole($roles, ?string $guard = null)
 */
	class User extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $estudiante_id
 * @property int $seccion_id
 * @property string $tipo_vocero
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Estudiante $estudiante
 * @property-read \App\Models\Seccion $seccion
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vocero newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vocero newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vocero query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vocero whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vocero whereEstudianteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vocero whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vocero whereSeccionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vocero whereTipoVocero($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vocero whereUpdatedAt($value)
 */
	class Vocero extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $docente_unidad_curricular_id
 * @property int $seccion_id
 * @property int $horas
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Seccion $seccion
 * @method static \Illuminate\Database\Eloquent\Builder<static>|carga_docente newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|carga_docente newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|carga_docente query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|carga_docente whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|carga_docente whereDocenteUnidadCurricularId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|carga_docente whereHoras($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|carga_docente whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|carga_docente whereSeccionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|carga_docente whereUpdatedAt($value)
 */
	class carga_docente extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $pnf_trayecto_id
 * @property int $max_estudiantes_seccion
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|configuracion_pnf newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|configuracion_pnf newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|configuracion_pnf query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|configuracion_pnf whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|configuracion_pnf whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|configuracion_pnf whereMaxEstudiantesSeccion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|configuracion_pnf wherePnfTrayectoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|configuracion_pnf whereUpdatedAt($value)
 */
	class configuracion_pnf extends \Eloquent {}
}

