<?php

namespace App\Traits;

use App\Models\Bitacora;
use Illuminate\Support\Facades\Auth;

trait Auditable
{
    /**
     * Se ejecuta automáticamente al inicializar el modelo.
     */
    public static function bootAuditable()
    {
        static::created(function ($model) {
            $nuevos = static::filtrarAtributosSensibles($model->getAttributes());
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

            $anterioresLimpios = static::filtrarAtributosSensibles($anteriores);
            $nuevosLimpios = static::filtrarAtributosSensibles($cambios);

            static::registrarAccion($model, 'MODIFICAR', $anterioresLimpios, $nuevosLimpios);
        });

        static::deleted(function ($model) {
            $anteriores = static::filtrarAtributosSensibles($model->getOriginal());
            static::registrarAccion($model, 'ELIMINAR', $anteriores, null);
        });
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
            'user_id'          => $user?->id,
            'accion'           => $accion,
            'tabla'            => $model->getTable(),
            'registro_id'      => $model->getKey(),
            'datos_anteriores' => $anteriores ?: null, // Con $casts en Bitacora no requiere json_encode
            'datos_nuevos'     => $nuevos ?: null,
            'ip_address'       => request()->ip(),
        ]);
    }
}
