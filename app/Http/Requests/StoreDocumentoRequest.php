<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDocumentoRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'pnf_id' => [
                Rule::requiredIf(fn () => ! $this->user()?->pnf),
                'nullable',
                'integer',
                'exists:pnfs,id',
            ],
            'archivo' => [
                'required',
                'file',
                'mimes:pdf,doc,docx,xls,xlsx,csv,jpg,jpeg,png,webp,gif',
                'max:10240', // Máximo 10 MB
            ],
            'nombre' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Mensajes personalizados de validación.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'pnf_id.required' => 'Debe seleccionar un PNF para asociar el documento.',
            'pnf_id.exists' => 'El PNF seleccionado no existe.',
            'archivo.required' => 'Debe adjuntar un documento.',
            'archivo.mimes' => 'El documento debe ser PDF, Word, Excel o una imagen.',
            'archivo.max' => 'El documento no debe pesar más de 10 MB.',
        ];
    }
}
