<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InsuranceFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->hasRole('admin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $insuranceId = $this->route('insurance');

        return [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:insurance_companies,code,'.$insuranceId,
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'contact_person' => 'nullable|string|max:255',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:255',
            'default_coverage_percentage' => 'nullable|numeric|min:0|max:100',
            'default_copay_amount' => 'nullable|numeric|min:0',
            'is_active' => 'required|boolean',
            'coverage_types' => 'nullable|array',
            'notes' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre de la aseguradora es requerido',
            'code.required' => 'El código es requerido',
            'code.unique' => 'Este código ya está siendo utilizado',
            'email.email' => 'Debe ser una dirección de email válida',
            'contact_email.email' => 'Debe ser una dirección de email válida',
            'is_active.required' => 'El estado es requerido',
            'default_coverage_percentage.max' => 'El porcentaje de cobertura no puede ser mayor a 100',
            'default_copay_amount.min' => 'El monto de copago no puede ser negativo',
        ];
    }
}
