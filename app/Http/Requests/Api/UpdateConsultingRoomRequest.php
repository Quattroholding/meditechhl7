<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateConsultingRoomRequest extends FormRequest
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
            'branch_id' => 'required|exists:branches,id',
            'name' => 'required|string|max:255',
            'number' => 'nullable|string|max:50',
            'floor' => 'nullable|string|max:50',
            'active' => 'nullable|boolean',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'branch_id.required' => 'La sucursal es obligatoria.',
            'branch_id.exists' => 'La sucursal seleccionada no existe.',
            'name.required' => 'El nombre del consultorio es obligatorio.',
            'name.max' => 'El nombre no puede exceder los 255 caracteres.',
            'number.max' => 'El número no puede exceder los 50 caracteres.',
            'floor.max' => 'El piso no puede exceder los 50 caracteres.',
            'active.boolean' => 'El estado activo debe ser verdadero o falso.',
        ];
    }
}
