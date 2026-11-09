<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConfiguracionPnfResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'pnf_id' => $this->pnf_id,
            'aula_max' => (int) $this->capacidad_max_aula,
            'porcentaje_aprobacion' => (float) $this->tasa_aprobacion_base,
            'porcentaje_reprobacion' => (float) $this->tasa_reprobacion_base,
            'ultima_actualizacion' => $this->updated_at->format('d/m/Y h:i A'),
        ];
    }
}
