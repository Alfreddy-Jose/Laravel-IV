<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StorePnfRequest extends FormRequest
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
            'codigo' => 'required|numeric|unique:pnfs,codigo',
            'nombre' => 'required|string',
            'abreviado' => 'required|string|min:4',
            'abreviado_coord' => 'required|string|min:3',
            'logo' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ];
    }

    public function attributes()
    {
        return [
            'codigo' => 'código',
            'nombre' => 'nombre',
            'abreviado' => 'abreviado',
            'abreviado_coord' => 'abreviado coordinación'
        ];
    }
}
