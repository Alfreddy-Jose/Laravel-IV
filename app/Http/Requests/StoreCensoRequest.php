<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreCensoRequest extends FormRequest
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
    /**
         * Obtiene las reglas de validación que se aplicarán a la solicitud.
         */
    public function rules(): array
    {
        return [
            'estudiante_id' => ['required', 'exists:estudiantes,id'],
            'lapso_id'      => ['required', 'exists:lapso_academicos,id'],
            'nombre'        => ['required', 'string', 'max:255'],
            'tipo_censo'    => ['required', 'string'],
            // Validamos que 'detalles' sea un objeto/arreglo estructurado obligatorio
            'detalles'      => ['required', 'array'],
            'observaciones' => ['nullable', 'string']
        ];
    }

    /**
     * Personaliza los nombres de los atributos para los mensajes de error.
     */
    public function attributes(): array
    {
        return [
            'estudiante_id' => 'Estudiante',
            'lapso_id'      => 'Lapso Académico',
            'nombre'        => 'Nombre del Censo',
            'tipo_censo'    => 'Tipo de Censo',
            'detalles'      => 'Detalles del Formulario',
        ];
    }

    /**
     * Mensajes personalizados que Formik leerá directamente en el frontend.
     */
    public function messages(): array
    {
        return [
            'required' => 'El campo :attribute es obligatorio.',
            'exists'   => 'El :attribute seleccionado no es válido o no existe en el sistema.',
            'array'    => 'Los :attribute deben ser un objeto de datos válido.',
        ];
    }
}
