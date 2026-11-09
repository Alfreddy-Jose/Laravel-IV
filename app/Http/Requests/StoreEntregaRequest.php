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

    protected function prepareForValidation(): void
    {
        $this->merge([
            'entregado' => filter_var($this->entregado, FILTER_VALIDATE_BOOLEAN),
            $this->input('lapso_academico_id') ?? $this->header('X-Lapso-Id')
        ]);
    }

    public function rules(): array
    {
        return [
            'entregado' => 'required|boolean',
            'tipo_documento_id' => 'required|integer',
            'docente_id' => 'required|integer',
            'unidad_curricular_id' => 'integer',
            'lapso_academico_id' => 'required|integer',
            'seccion_id' => 'nullable|integer',
            'observaciones' => 'nullable|string',
            'archivo' => 'nullable|file|mimes:pdf,doc,docx,jpg,png|max:5120'
        ];
    }

    /**
     * Mensajes personalizados en español para las validaciones.
     */
    public function messages(): array
    {
        return [
            'archivo.mimes' => 'El archivo debe tener un formato válido: pdf, doc, docx, jpg, png.',
            'archivo.max' => 'El archivo es demasiado pesado. El límite máximo permitido es de 5 MB.',
            'archivo.file' => 'El recurso adjuntado no es un archivo válido.'
        ];
    }
}
