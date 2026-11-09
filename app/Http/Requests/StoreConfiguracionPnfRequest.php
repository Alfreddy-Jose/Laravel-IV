<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreConfiguracionPnfRequest extends FormRequest
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
            'configuraciones' => 'required|array',
            'configuraciones.*.pnf_trayecto_id' => 'required|integer',
            'configuraciones.*.max_estudiantes_seccion' => 'required|integer|min:1',
        ];
    }
}
