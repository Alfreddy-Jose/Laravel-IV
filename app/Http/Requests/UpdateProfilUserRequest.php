<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfilUserRequest extends FormRequest
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
        $userId = $this->route('user');

        return [
            'name' => 'required|string|max:255|regex:/^[a-zA-Z\sáéíóúÁÉÍÓÚñÑ]+$/',
            'email' => [
                'required',
                'email',
                'max:255',
                // Si $userId es null (creación), solo valida unicidad.
                // Si $userId existe (edición), ignora ese ID.
                Rule::unique('users', 'email')->ignore($userId),
            ]
        ];
    }
}
