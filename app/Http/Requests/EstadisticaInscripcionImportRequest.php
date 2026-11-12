<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EstadisticaInscripcionImportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'lapso_id' => 'required|exists:lapso_academicos,id',
            'archivo'  => [
                'required',
                'file',
                'mimes:xlsx,xls,csv',
                'max:10240', // Máximo 10 MB
            ],
            'confirmar_reemplazo' => ['nullable', 'boolean'],
            'pnf_id' => [
                Rule::requiredIf(fn () => ! $this->user()?->pnf),
                'nullable',
                'integer',
                'exists:pnfs,id',
            ],
        ];
    }

    public function messages(): array
    {
        return  [
            'lapso_id.required' => 'Debe indicar el lapso académico.',
            'lapso_id.exists'   => 'El lapso académico seleccionado no existe.',
            'archivo.required'  => 'Debe adjuntar un archivo de inscripción.',
            'archivo.mimes'     => 'El archivo debe ser de formato .xlsx, .xls o .csv.',
            'archivo.max'       => 'El archivo no debe pesar más de 10 MB.',
            'pnf_id.required'   => 'Debe seleccionar un PNF para asociar el archivo.',
            'pnf_id.exists'     => 'El PNF seleccionado no existe.',
        ];
    }
}
