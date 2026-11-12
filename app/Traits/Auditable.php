<?php

namespace App\Traits;

use App\Models\Bitacora;
use App\Support\ClientIp;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

trait Auditable
{
    /**
     * Se ejecuta automáticamente al inicializar el modelo.
     */
    public static function bootAuditable()
    {
        static::created(function ($model) {
            // Al insertar se guardan todos los atributos del registro recién creado.
            $nuevos = static::prepararDatos($model, $model->getAttributes());
            static::registrarAccion($model, 'INSERTAR', null, $nuevos);
        });

        static::updated(function ($model) {
            $cambios = $model->getChanges();

            // Ignorar si solo cambiaron los timestamps automáticamente
            unset($cambios['updated_at']);
            if (empty($cambios)) {
                return;
            }

            // Obtener únicamente los valores originales de los campos que cambiaron
            $anteriores = array_intersect_key($model->getOriginal(), $cambios);

            $anterioresLimpios = static::prepararDatos($model, $anteriores);
            $nuevosLimpios = static::prepararDatos($model, $cambios);

            static::registrarAccion($model, 'MODIFICAR', $anterioresLimpios, $nuevosLimpios);
        });

        static::deleted(function ($model) {
            $anteriores = static::prepararDatos($model, $model->getOriginal());
            static::registrarAccion($model, 'ELIMINAR', $anteriores, null);
        });
    }

    /**
     * Prepara los atributos antes de guardarlos en la bitácora.
     *
     * Además de ocultar campos sensibles, resuelve las claves foráneas
     * (atributos terminados en `_id`) a un nombre legible y lo guarda en un
     * campo hermano `{atributo}_label`. De este modo el histórico queda
     * autocontenido y no necesita JOIN al momento de mostrarse.
     *
     * Un modelo puede sobrescribir el mapeo automático declarando la propiedad
     * estática `$auditableDisplay`. Por ejemplo:
     *
     *     protected static array $auditableDisplay = [
     *         'persona_id' => ['relation' => 'persona', 'label' => 'nombre_completo'],
     *         'pnf_id'     => ['relation' => 'pnf',     'label' => 'nombre'],
     *     ];
     */
    protected static function prepararDatos($model, array $attributes): array
    {
        $attributes = static::filtrarAtributosSensibles($attributes);

        // Evita duplicar etiquetas en actualizaciones: siempre se recalculan.
        foreach (array_keys($attributes) as $key) {
            if (str_ends_with($key, '_label')) {
                unset($attributes[$key]);
            }
        }

        $configuracion = static::configuracionDisplay($model);

        foreach ($attributes as $key => $value) {
            if (! str_ends_with($key, '_id') || ! is_scalar($value) || $value === '') {
                continue;
            }

            try {
                $config = $configuracion[$key] ?? [];
                $relation = $config['relation'] ?? Str::camel(str_replace('_id', '', $key));
                $labelAttr = $config['label'] ?? null;

                // Si el modelo no define la relación simplemente se conserva el ID crudo.
                if (! method_exists($model, $relation)) {
                    continue;
                }

                $related = static::resolverRelacion($model, $relation, $key, $value);
                $label = static::extraerLabel($related, $labelAttr);

                if ($label !== null && $label !== '') {
                    $attributes[$key.'_label'] = $label;
                }
            } catch (\Throwable $e) {
                // Un fallo de relación nunca debe romper el guardado del modelo.
                \Log::warning('Auditable: no se pudo resolver la etiqueta de '.$key, [
                    'modelo' => $model::class,
                    'valor' => $value,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $attributes;
    }

    /**
     * Obtiene la configuración de mapeo declarada por el modelo, si existe.
     */
    protected static function configuracionDisplay($model): array
    {
        if (! property_exists($model, 'auditableDisplay')) {
            return [];
        }

        $clase = $model::class;

        return is_array($clase::$auditableDisplay) ? $clase::$auditableDisplay : [];
    }

    /**
     * Carga el registro relacionado que corresponde al valor indicado.
     *
     * En MODIFICAR los datos anteriores apuntan a otro registro distinto del
     * actual, por lo que se consulta la clave del modelo relacionado en lugar
     * de reutilizar la relación ya cargada.
     */
    protected static function resolverRelacion($model, string $relation, string $key, $value)
    {
        if ((string) $model->getAttribute($key) === (string) $value) {
            return $model->{$relation};
        }

        $relacion = $model->{$relation}();

        if ($relacion instanceof BelongsTo) {
            return $relacion->getRelated()
                ->newQuery()
                ->where($relacion->getOwnerKeyName(), $value)
                ->first();
        }

        // Relaciones múltiples (p. ej. belongsToMany): se usa la relación cargada.
        return $model->{$relation};
    }

    /**
     * Extrae un nombre legible del modelo relacionado.
     *
     * Prioriza el atributo explícito, luego `nombre_completo`, luego la
     * combinación `nombre + apellido` y finalmente una lista de campos comunes.
     */
    protected static function extraerLabel($model, ?string $attribute = null): ?string
    {
        if (! $model) {
            return null;
        }

        if ($model instanceof Collection) {
            $model = $model->first();

            if (! $model) {
                return null;
            }
        }

        if ($attribute) {
            $valor = $model->getAttribute($attribute);

            if (static::valorLegible($valor)) {
                return (string) $valor;
            }
        }

        $nombreCompleto = $model->getAttribute('nombre_completo');

        if (static::valorLegible($nombreCompleto)) {
            return (string) $nombreCompleto;
        }

        $nombre = $model->getAttribute('nombre');
        $apellido = $model->getAttribute('apellido');
        $combinado = trim(($nombre ?? '').' '.($apellido ?? ''));

        if ($combinado !== '') {
            return $combinado;
        }

        foreach (['nombre_sede', 'nombre_lapso', 'nombre', 'name', 'municipio', 'ano', 'descripcion'] as $campo) {
            $valor = $model->getAttribute($campo);

            if (static::valorLegible($valor)) {
                return (string) $valor;
            }
        }

        return (string) $model->getKey();
    }

    /**
     * Indica si un valor puede mostrarse como etiqueta.
     */
    protected static function valorLegible($valor): bool
    {
        return is_scalar($valor) && trim((string) $valor) !== '';
    }

    /**
     * Remueve campos sensibles o innecesarios de los registros.
     */
    protected static function filtrarAtributosSensibles(array $atributos): array
    {
        $ocultar = ['password', 'remember_token', 'created_at', 'updated_at', 'deleted_at'];

        return array_diff_key($atributos, array_flip($ocultar));
    }

    /**
     * Guarda la entrada en la bitácora.
     */
    protected static function registrarAccion($model, string $accion, ?array $anteriores = null, ?array $nuevos = null): void
    {
        $user = Auth::user() ?? request()->user();

        Bitacora::create([
            'user_id' => $user?->id,
            'accion' => $accion,
            'tabla' => $model->getTable(),
            'registro_id' => $model->getKey(),
            'datos_anteriores' => $anteriores ?: null, // Con $casts en Bitacora no requiere json_encode
            'datos_nuevos' => $nuevos ?: null,
            'ip_address' => ClientIp::fromRequest(),
        ]);
    }
}
