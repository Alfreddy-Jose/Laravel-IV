<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class LapsoAcademicoScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        // Verificamos si la petición HTTP actual trae el Header de React
        if (request()->hasHeader('X-Lapso-Id')) {
            $lapsoId = request()->header('X-Lapso-Id');

            // Especificamos el nombre de la tabla para evitar ambigüedades en los JOINs
            $builder->where($model->getTable() . '.lapso_academico_id', $lapsoId);
        }
    }
}
