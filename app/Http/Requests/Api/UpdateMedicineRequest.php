<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMedicineRequest extends FormRequest
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
        $medicineId = $this->route('medicine')->id ?? $this->route('medicine');

        return [
            'ndc_code' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('medicines', 'ndc_code')->ignore($medicineId),
            ],
            'home_name' => 'nullable|string|max:255',
            'generic_name' => 'required|string|max:255',
            'type' => 'required|string|max:100',
            'mgs' => 'required|numeric|min:0',
            'mgs_type' => 'required|string|max:50',
            'active' => 'nullable|boolean',
            'narcotic' => 'nullable|boolean',
            'client_id' => 'required|exists:clients,id',
            'price' => 'nullable|numeric|min:0',
            'product_type' => 'nullable|string|max:100',
            'usage_indications' => 'nullable|string',
            'porpuse' => 'nullable|string',
            'indication_and_usage' => 'nullable|string',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'ndc_code.unique' => 'El código NDC ya está en uso.',
            'generic_name.required' => 'El nombre genérico es obligatorio.',
            'generic_name.max' => 'El nombre genérico no puede exceder los 255 caracteres.',
            'type.required' => 'El tipo de medicamento es obligatorio.',
            'type.max' => 'El tipo no puede exceder los 100 caracteres.',
            'mgs.required' => 'La cantidad en mg es obligatoria.',
            'mgs.numeric' => 'La cantidad debe ser un número.',
            'mgs.min' => 'La cantidad no puede ser negativa.',
            'mgs_type.required' => 'El tipo de medida es obligatorio.',
            'mgs_type.max' => 'El tipo de medida no puede exceder los 50 caracteres.',
            'client_id.required' => 'El cliente es obligatorio.',
            'client_id.exists' => 'El cliente seleccionado no existe.',
            'price.numeric' => 'El precio debe ser un número.',
            'price.min' => 'El precio no puede ser negativo.',
            'active.boolean' => 'El estado activo debe ser verdadero o falso.',
            'narcotic.boolean' => 'El campo narcótico debe ser verdadero o falso.',
        ];
    }
}
