<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEntregaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }



    public function rules()
    {
        return [
            'entregado' => 'required|boolean',
            'docente_id' => 'required|exists:docentes,id',
            'lapso_academico_id' => 'required|exists:lapso_academicos,id',
            'tipo_documento_id' => 'required|exists:tipo_documentos,id',
            'seccion_id' => 'nullable|exists:seccions,id',
        ];
    }
}
