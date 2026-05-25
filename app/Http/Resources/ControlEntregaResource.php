<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ControlEntregaResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'entregado' => (bool) $this->entregado,
            'observaciones' => $this->observaciones,
            'docente' => $this->docente->nombre, 
            'documento' => $this->tipoDocumento->nombre,
            'seccion' => $this->seccion ? $this->seccion->nro_seccion : 'N/A',
        ];
    }
}
