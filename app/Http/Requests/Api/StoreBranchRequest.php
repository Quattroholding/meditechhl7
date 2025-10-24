<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreBranchRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'client_id' => 'required|exists:clients,id',
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'type' => 'nullable|string|max:100',
            'active' => 'nullable|boolean',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'client_id.required' => 'El cliente es obligatorio.',
            'client_id.exists' => 'El cliente seleccionado no existe.',
            'name.required' => 'El nombre de la sucursal es obligatorio.',
            'name.max' => 'El nombre no puede exceder los 255 caracteres.',
            'phone.max' => 'El teléfono no puede exceder los 50 caracteres.',
            'address.max' => 'La dirección no puede exceder los 500 caracteres.',
            'type.max' => 'El tipo no puede exceder los 100 caracteres.',
            'active.boolean' => 'El estado activo debe ser verdadero o falso.',
        ];
    }
}
